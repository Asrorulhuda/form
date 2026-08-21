<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

/**
 * WordTemplateEngine
 * Core Engine for Microsoft Word (.docx) templates, variable extraction with XML run-splitting resolution,
 * template replacement, and PDF conversion.
 */
class WordTemplateEngine
{
    /**
     * Extract all {{variable}} patterns from a .docx file.
     * Accurately handles variables even if split across multiple Word XML runs (<w:r>).
     */
    public static function extractVariables(string $docxPath): array
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        if (!file_exists($docxPath)) {
            throw new \Exception("Berkas template Word tidak ditemukan di: {$docxPath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) {
            throw new \Exception("Gagal membuka berkas .docx. Pastikan berkas tidak rusak.");
        }

        // XML parts to scan: body (document.xml), headers (header1.xml, ...), footers (footer1.xml, ...)
        $xmlParts = ['word/document.xml'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if ($filename && preg_match('#^word/(header|footer)\d+\.xml$#', $filename)) {
                $xmlParts[] = $filename;
            }
        }

        $allVariables = [];

        foreach ($xmlParts as $xmlFile) {
            $xmlContent = $zip->getFromName($xmlFile);
            if (!$xmlContent || strpos($xmlContent, '{') === false) {
                unset($xmlContent);
                continue;
            }

            // Clean XML by stripping tags between curly braces to resolve Word run-splitting
            $normalizedXml = preg_replace_callback('/\{\{([^{}]+)\}\}/s', function ($matches) {
                return '{{' . strip_tags($matches[1]) . '}}';
            }, $xmlContent);

            $cleanText = strip_tags($normalizedXml ?: $xmlContent);
            preg_match_all('/\{\{([^{}]+)\}\}/', $cleanText, $matches1);
            if (!empty($matches1[1])) {
                foreach ($matches1[1] as $v) {
                    $cleanV = trim(strip_tags($v));
                    if ($cleanV !== '') {
                        $allVariables[$cleanV] = true;
                    }
                }
            }
            unset($xmlContent, $normalizedXml, $cleanText, $matches1);
        }

        $zip->close();
        unset($zip);

        // Also use TemplateProcessor's built-in variable parser
        try {
            $processor = new TemplateProcessor($docxPath);
            $processor->setMacroChars('{{', '}}');
            $tpVars = $processor->getVariables();
            foreach ($tpVars as $v) {
                $allVariables[trim($v)] = true;
            }
            unset($processor);
        } catch (\Throwable $e) {
            // Ignore fallback to regex
        }

        gc_collect_cycles();

        return array_values(array_keys($allVariables));
    }

    /**
     * Render and generate output .docx file from template and variable values.
     * Preserves all formatting, tables, headers, footers, margins, fonts, and images.
     */
    public static function renderDocx(string $templateDocxPath, array $variableValues, string $outputDocxPath): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        if (!file_exists($templateDocxPath)) {
            throw new \Exception("Template Word tidak ditemukan: {$templateDocxPath}");
        }

        // Ensure output directory exists
        $outputDir = dirname($outputDocxPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Pre-normalize template XML to ensure no split runs break replacement
        $tempCleanDocx = null;
        try {
            $tempCleanDocx = self::normalizeDocxVariables($templateDocxPath);
        } catch (\Throwable $e) {
            $tempCleanDocx = null;
        }

        $sourceFile = ($tempCleanDocx && file_exists($tempCleanDocx)) ? $tempCleanDocx : $templateDocxPath;
        $processor = new TemplateProcessor($sourceFile);
        $processor->setMacroChars('{{', '}}');

        // Detect all variables actually existing in the template to avoid unnecessary preg_replace runs
        $existingVars = [];
        try {
            $rawVars = $processor->getVariables();
            foreach ($rawVars as $rv) {
                $existingVars[trim($rv)] = true;
            }
        } catch (\Throwable $e) {
            $existingVars = [];
        }

        foreach ($variableValues as $key => $val) {
            if ($key === '__qr_image_path') {
                continue;
            }
            $cleanKey = trim($key, '{} ');
            $valueStr = is_scalar($val) ? (string)$val : json_encode($val);

            // Set both the exact key, and space/underscore variations
            $variations = [
                $cleanKey,
                str_replace('_', ' ', $cleanKey),
                str_replace(' ', '_', $cleanKey),
                ucwords(str_replace('_', ' ', $cleanKey)),
                strtolower(str_replace(' ', '_', $cleanKey)),
            ];

            $applied = false;
            foreach (array_unique($variations) as $varKey) {
                // If template variables detected, only set values that actually exist in the template
                if (!empty($existingVars) && !isset($existingVars[$varKey])) {
                    continue;
                }

                if (str_contains($valueStr, "\n")) {
                    $lines = explode("\n", str_replace("\r", "", $valueStr));
                    $processor->setValue($varKey, implode('</w:t><w:br/><w:t>', array_map('htmlspecialchars', $lines)));
                } else {
                    $processor->setValue($varKey, htmlspecialchars($valueStr));
                }
                $applied = true;
            }

            // If none of variations matched and detected vars list was empty, set standard key
            if (!$applied && empty($existingVars)) {
                if (str_contains($valueStr, "\n")) {
                    $lines = explode("\n", str_replace("\r", "", $valueStr));
                    $processor->setValue($cleanKey, implode('</w:t><w:br/><w:t>', array_map('htmlspecialchars', $lines)));
                } else {
                    $processor->setValue($cleanKey, htmlspecialchars($valueStr));
                }
            }
        }

        // Replace QR Code Image if {{qr_code}} or {{qr_verifikasi}} tag is in the template
        $qrPath = $variableValues['__qr_image_path'] ?? null;
        if ($qrPath && file_exists($qrPath)) {
            $qrTags = ['qr_code', 'qr_verifikasi', 'qr', 'qrcode', 'QR Code', 'QR Verifikasi'];
            foreach ($qrTags as $qrTag) {
                if (!empty($existingVars) && !isset($existingVars[$qrTag])) {
                    continue;
                }
                try {
                    $processor->setImageValue($qrTag, [
                        'path'   => $qrPath,
                        'width'  => 52,
                        'height' => 52,
                        'ratio'  => false,
                    ]);
                } catch (\Throwable $e) {
                    // Tag might not exist in template, continue
                }
            }
        }

        $processor->saveAs($outputDocxPath);
        unset($processor);

        if ($tempCleanDocx && file_exists($tempCleanDocx)) {
            @unlink($tempCleanDocx);
        }

        gc_collect_cycles();

        return $outputDocxPath;
    }

    /**
     * Pre-normalize XML inside DOCX to merge fragmented {{variable}} runs seamlessly
     */
    private static function normalizeDocxVariables(string $sourcePath): ?string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        try {
            $tempDir = BASE_PATH . '/storage/temp';
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0777, true);
            }

            $tempPath = $tempDir . '/norm_' . bin2hex(random_bytes(8)) . '.docx';
            if (!@copy($sourcePath, $tempPath)) {
                return null;
            }

            $zip = new ZipArchive();
            if ($zip->open($tempPath) !== true) {
                @unlink($tempPath);
                return null;
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if ($filename && preg_match('#^word/(document|header\d+|footer\d+)\.xml$#', $filename)) {
                    $xml = $zip->getFromIndex($i);
                    if ($xml && strpos($xml, '{') !== false) {
                        // Merge split runs inside {{...}}
                        $cleanedXml = preg_replace_callback('/\{(\{[^{}]+\})\}/s', function ($m) {
                            return strip_tags($m[0]);
                        }, $xml);

                        if ($cleanedXml !== null && $cleanedXml !== $xml) {
                            $zip->deleteIndex($i);
                            $zip->addFromString($filename, $cleanedXml);
                        }
                        unset($cleanedXml);
                    }
                    unset($xml);
                }
            }

            $zip->close();
            unset($zip);
            gc_collect_cycles();

            return $tempPath;
        } catch (\Throwable $e) {
            // Graceful fallback to original file if memory or zip manipulation fails
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            return null;
        }
    }

    /**
     * Convert generated .docx file to .pdf using LibreOffice Headless if available.
     * Returns the output PDF path if successful, or null if LibreOffice is not available.
     */
    public static function convertToPdf(string $docxPath, string $outputPdfPath): ?string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);
        if (!file_exists($docxPath)) {
            return null;
        }

        $outputDir = dirname($outputPdfPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Possible LibreOffice executable paths on Windows and Linux
        $libreOfficePaths = [
            'soffice',
            'libreoffice',
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            'C:\\laragon\\bin\\libreoffice\\program\\soffice.exe',
        ];

        $executable = null;
        foreach ($libreOfficePaths as $path) {
            if (file_exists($path)) {
                $executable = '"' . $path . '"';
                break;
            }
        }

        if (!$executable) {
            // Check if soffice is in system PATH
            $test = @shell_exec('soffice --version 2>&1');
            if ($test && stripos($test, 'LibreOffice') !== false) {
                $executable = 'soffice';
            }
        }

        if (!$executable) {
            return null; // LibreOffice not installed on server
        }

        // Execute LibreOffice headless conversion
        $cmd = "{$executable} --headless --convert-to pdf --outdir " . escapeshellarg($outputDir) . " " . escapeshellarg($docxPath) . " 2>&1";
        @shell_exec($cmd);

        $expectedPdf = $outputDir . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
        if (file_exists($expectedPdf)) {
            if ($expectedPdf !== $outputPdfPath) {
                @rename($expectedPdf, $outputPdfPath);
            }
            return $outputPdfPath;
        }

        return null;
    }

    /**
     * Resolve final variable values from mappings, form response, user, and settings
     */
    public static function resolveVariableValues(array $variableMappings, array $submittedData = [], ?object $user = null, array $settings = [], string $documentNumber = ''): array
    {
        $monthsId = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $d = (int)date('j');
        $m = (int)date('n');
        $y = date('Y');
        $tanggalSurat = "{$d} " . ($monthsId[$m] ?? date('F')) . " {$y}";

        $values = [];

        foreach ($variableMappings as $var) {
            $varName = is_array($var) ? $var['variable_name'] : $var->variable_name;
            $sourceType = is_array($var) ? ($var['source_type'] ?? 'form_response') : ($var->source_type ?? 'form_response');
            $sourceKey = is_array($var) ? ($var['source_key'] ?? $varName) : ($var->source_key ?? $varName);
            $defaultValue = is_array($var) ? ($var['default_value'] ?? '') : ($var->default_value ?? '');

            $finalVal = '';

            switch ($sourceType) {
                case 'system':
                    if (in_array($sourceKey, ['tanggal_surat', 'tanggal'])) {
                        $finalVal = $tanggalSurat;
                    } elseif ($sourceKey === 'nomor_surat' || $sourceKey === 'nomor_dokumen') {
                        $finalVal = $documentNumber ?: 'DOC/' . date('Ym') . '/0001';
                    } elseif ($sourceKey === 'bulan') {
                        $finalVal = $monthsId[$m] ?? date('F');
                    } elseif ($sourceKey === 'tahun') {
                        $finalVal = $y;
                    } elseif ($sourceKey === 'tanggal_angka') {
                        $finalVal = date('d/m/Y');
                    } else {
                        $finalVal = $defaultValue ?: $tanggalSurat;
                    }
                    break;

                case 'user':
                    if ($user) {
                        if ($sourceKey === 'user_name' || $sourceKey === 'name') $finalVal = $user->name ?? '';
                        elseif ($sourceKey === 'user_email' || $sourceKey === 'email') $finalVal = $user->email ?? '';
                        else $finalVal = $user->{$sourceKey} ?? $defaultValue;
                    } else {
                        $finalVal = $defaultValue;
                    }
                    break;

                case 'setting':
                    $settingCandidates = [
                        $sourceKey,
                        $varName,
                        str_replace(' ', '_', $sourceKey),
                        str_replace('_', ' ', $sourceKey),
                        strtolower(str_replace(' ', '_', $sourceKey)),
                        strtolower(str_replace(' ', '_', $varName)),
                    ];
                    $sFound = null;
                    foreach ($settingCandidates as $sCand) {
                        if (isset($settings[$sCand]) && $settings[$sCand] !== '') {
                            $sFound = $settings[$sCand];
                            break;
                        }
                    }
                    if ($sFound !== null) {
                        $finalVal = $sFound;
                    } else {
                        // Fallback to submitted form data if setting is not yet defined
                        foreach ($settingCandidates as $sCand) {
                            if (isset($submittedData[$sCand]) && $submittedData[$sCand] !== '') {
                                $finalVal = $submittedData[$sCand];
                                break;
                            }
                        }
                        if ($finalVal === '') $finalVal = $defaultValue;
                    }
                    break;

                case 'custom':
                    $finalVal = $defaultValue;
                    break;

                case 'form_response':
                default:
                    $candidates = [
                        $sourceKey,
                        $varName,
                        str_replace(' ', '_', $sourceKey),
                        str_replace('_', ' ', $sourceKey),
                        strtolower(str_replace(' ', '_', $sourceKey)),
                        strtolower(str_replace(' ', '_', $varName)),
                        ucwords(str_replace('_', ' ', $sourceKey)),
                    ];
                    $found = null;
                    foreach ($candidates as $cand) {
                        if (isset($submittedData[$cand]) && $submittedData[$cand] !== '') {
                            $found = $submittedData[$cand];
                            break;
                        }
                    }
                    $finalVal = $found !== null ? $found : $defaultValue;
                    break;
            }

            $values[$varName] = $finalVal;
        }

        // Always provide fallback standard system tags & verification tags
        if (!isset($values['nomor_surat'])) $values['nomor_surat'] = $documentNumber;
        if (!isset($values['nomor_dokumen'])) $values['nomor_dokumen'] = $documentNumber;
        if (!isset($values['tanggal_surat'])) $values['tanggal_surat'] = $tanggalSurat;
        if (!isset($values['tahun'])) $values['tahun'] = $y;
        if (!isset($values['status_dokumen'])) $values['status_dokumen'] = 'SAH & TERVERIFIKASI';
        if (!isset($values['tanggal_verifikasi'])) $values['tanggal_verifikasi'] = date('d/m/Y H:i') . ' WIB';

        return $values;
    }
}

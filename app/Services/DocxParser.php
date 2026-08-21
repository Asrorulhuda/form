<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use DOMXPath;

/**
 * DocxParser - Converts Microsoft Word (.docx) documents into clean styled HTML
 * Preserves Kop Surat, logos, images, tables, font families, font sizes, text alignments,
 * tab stops, bold, italic, underline, and dynamic variable tags ({{tag}}).
 */
class DocxParser
{
    /**
     * Parse a .docx file path and return high-fidelity HTML representation
     */
    public static function parseToHtml(string $filePath): string
    {
        @ini_set('memory_limit', '512M');

        if (!file_exists($filePath)) {
            throw new \Exception("Berkas Word tidak ditemukan.");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception("Gagal membuka berkas .docx. Pastikan berkas tidak rusak.");
        }

        // 1. Extract all media relationships (word/_rels/document.xml.rels)
        $images = self::extractImages($zip);

        // 2. Extract main document XML
        $xmlContent = $zip->getFromName('word/document.xml');

        // 3. Extract header XMLs if any
        $headerXmls = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#^word/header\d+\.xml$#', $name)) {
                $headerXmls[] = $zip->getFromName($name);
            }
        }

        $zip->close();

        if (!$xmlContent) {
            throw new \Exception("Format berkas Word tidak valid (word/document.xml tidak ditemukan).");
        }

        return self::convertXmlToHtml($xmlContent, $images, $headerXmls);
    }

    /**
     * Extract all images from zip archive and map relationship IDs to base64 data URIs
     */
    private static function extractImages(ZipArchive $zip): array
    {
        $images = [];
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');

        if ($relsXml) {
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadXML($relsXml, LIBXML_NOERROR | LIBXML_NOWARNING);

            foreach ($dom->getElementsByTagName('Relationship') as $rel) {
                $id = $rel->getAttribute('Id');
                $target = $rel->getAttribute('Target');
                $type = $rel->getAttribute('Type');

                if (str_contains($type, 'image') || preg_match('/\.(jpe?g|png|gif|webp|svg|bmp)$/i', $target)) {
                    $targetPath = 'word/' . ltrim($target, '/');
                    $imgData = $zip->getFromName($targetPath);
                    if ($imgData) {
                        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
                        $mime = match ($ext) {
                            'png' => 'image/png',
                            'gif' => 'image/gif',
                            'webp' => 'image/webp',
                            'svg' => 'image/svg+xml',
                            'bmp' => 'image/bmp',
                            default => 'image/jpeg',
                        };
                        $images[$id] = 'data:' . $mime . ';base64,' . base64_encode($imgData);
                    }
                }
            }
        }

        return $images;
    }

    /**
     * Convert document.xml to HTML with images, kop surat, and table layouts
     */
    private static function convertXmlToHtml(string $xml, array $images = [], array $headerXmls = []): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOWARNING | LIBXML_NOERROR);

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
        $xpath->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
        $xpath->registerNamespace('v', 'urn:schemas-microsoft-com:vml');

        $bodyNode = $xpath->query('//w:body')->item(0);
        if (!$bodyNode) {
            return '';
        }

        $html = '<div class="docx-letter" style="font-family: \'Times New Roman\', Times, serif; font-size: 12pt; line-height: 1.45; color: #000000; width: 100%; margin: 0 auto; background: #ffffff;">';

        // Render header content if available
        foreach ($headerXmls as $hXml) {
            $hDom = new DOMDocument();
            $hDom->loadXML($hXml, LIBXML_NOBLANKS | LIBXML_NOWARNING | LIBXML_NOERROR);
            $hXPath = new DOMXPath($hDom);
            $hXPath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $hXPath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $hXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $hXPath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
            $hXPath->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');

            $hBody = $hXPath->query('//w:hdr')->item(0);
            if ($hBody) {
                foreach ($hBody->childNodes as $node) {
                    if ($node->nodeName === 'w:p') {
                        $html .= self::parseParagraph($node, $hXPath, $images);
                    } elseif ($node->nodeName === 'w:tbl') {
                        $html .= self::parseTable($node, $hXPath, $images);
                    }
                }
            }
        }

        // Render main document body
        foreach ($bodyNode->childNodes as $node) {
            if ($node->nodeName === 'w:p') {
                $html .= self::parseParagraph($node, $xpath, $images);
            } elseif ($node->nodeName === 'w:tbl') {
                $html .= self::parseTable($node, $xpath, $images);
            }
        }

        $html .= '</div>';

        // Reconstruct any tags that might have extra whitespace
        $html = preg_replace('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', '{{$1}}', $html);

        return $html;
    }

    /**
     * Parse a paragraph node (<w:p>)
     */
    private static function parseParagraph(\DOMNode $pNode, DOMXPath $xpath, array $images = []): string
    {
        $align = 'left';
        $styles = [];
        $borderBottom = '';

        // Check paragraph properties (<w:pPr>)
        $pPr = $xpath->query('w:pPr', $pNode)->item(0);
        $isHeading = false;
        $headingLevel = 3;

        if ($pPr) {
            // Alignment
            $jc = $xpath->query('w:jc/@w:val', $pPr)->item(0);
            if ($jc) {
                $val = $jc->nodeValue;
                if ($val === 'center') $align = 'center';
                elseif ($val === 'right') $align = 'right';
                elseif ($val === 'both' || $val === 'distribute') $align = 'justify';
            }

            // Paragraph border (common in Kop Surat divider lines!)
            $pBdrBottom = $xpath->query('w:pBdr/w:bottom', $pPr)->item(0);
            if ($pBdrBottom) {
                $bdrVal = $pBdrBottom->getAttribute('w:val');
                $bdrSz = (int)$pBdrBottom->getAttribute('w:sz');
                $borderWidth = max(2, round($bdrSz / 4));
                if ($bdrVal === 'double') {
                    $borderBottom = 'border-bottom: 3px double #000000; padding-bottom: 6px; margin-bottom: 16px;';
                } else {
                    $borderBottom = "border-bottom: {$borderWidth}px solid #000000; padding-bottom: 6px; margin-bottom: 16px;";
                }
            }

            // Heading styles
            $pStyle = $xpath->query('w:pStyle/@w:val', $pPr)->item(0);
            if ($pStyle) {
                $styleVal = strtolower($pStyle->nodeValue);
                if (str_contains($styleVal, 'heading1') || $styleVal === '1') {
                    $isHeading = true;
                    $headingLevel = 1;
                } elseif (str_contains($styleVal, 'heading2') || $styleVal === '2') {
                    $isHeading = true;
                    $headingLevel = 2;
                } elseif (str_contains($styleVal, 'heading3') || $styleVal === '3') {
                    $isHeading = true;
                    $headingLevel = 3;
                } elseif (str_contains($styleVal, 'title')) {
                    $isHeading = true;
                    $headingLevel = 1;
                }
            }
        }

        // Parse runs and inline elements
        $innerHtml = '';
        foreach ($pNode->childNodes as $child) {
            if ($child->nodeName === 'w:r') {
                $innerHtml .= self::parseRun($child, $xpath, $images);
            } elseif ($child->nodeName === 'w:hyperlink') {
                $linkText = '';
                foreach ($child->childNodes as $rChild) {
                    if ($rChild->nodeName === 'w:r') {
                        $linkText .= self::parseRun($rChild, $xpath, $images);
                    }
                }
                $innerHtml .= '<a href="#" style="color: #4f46e5; text-decoration: underline;">' . $linkText . '</a>';
            }
        }

        // If paragraph only contains an image drawing, wrap centered
        if (str_contains($innerHtml, '<img ') && trim(strip_tags($innerHtml, '<img>')) === '') {
            return "<div style=\"text-align: center; margin-bottom: 14px; {$borderBottom}\">{$innerHtml}</div>";
        }

        // If paragraph is completely empty, render empty space
        if (trim($innerHtml) === '') {
            return '<div style="height: 12px;"></div>';
        }

        $styleStr = "text-align: {$align}; margin: 0 0 6px 0; line-height: 1.5; {$borderBottom}";

        if ($isHeading) {
            $fontSize = match($headingLevel) {
                1 => '18pt',
                2 => '15pt',
                default => '13pt',
            };
            return "<h{$headingLevel} style=\"{$styleStr} font-size: {$fontSize}; font-weight: bold; margin: 10px 0 6px 0;\">{$innerHtml}</h{$headingLevel}>";
        }

        return "<p style=\"{$styleStr}\">{$innerHtml}</p>";
    }

    /**
     * Parse a text run node (<w:r>)
     */
    private static function parseRun(\DOMNode $rNode, DOMXPath $xpath, array $images = []): string
    {
        $text = '';
        $rPr = $xpath->query('w:rPr', $rNode)->item(0);

        $isBold = false;
        $isItalic = false;
        $isUnderline = false;
        $isStrike = false;
        $fontSize = null;
        $fontColor = null;
        $fontFamily = null;

        if ($rPr) {
            $isBold = ($xpath->query('w:b', $rPr)->length > 0 && $xpath->query('w:b[@w:val="0" or @w:val="false"]', $rPr)->length === 0);
            $isItalic = ($xpath->query('w:i', $rPr)->length > 0 && $xpath->query('w:i[@w:val="0" or @w:val="false"]', $rPr)->length === 0);
            $isUnderline = ($xpath->query('w:u', $rPr)->length > 0 && $xpath->query('w:u[@w:val="none"]', $rPr)->length === 0);
            $isStrike = ($xpath->query('w:strike', $rPr)->length > 0);

            // Font size (<w:sz w:val="24"/> => 12pt)
            $sz = $xpath->query('w:sz/@w:val', $rPr)->item(0);
            if ($sz) {
                $pt = round(((int)$sz->nodeValue) / 2);
                $fontSize = "{$pt}pt";
            }

            // Font color (<w:color w:val="FF0000"/>)
            $color = $xpath->query('w:color/@w:val', $rPr)->item(0);
            if ($color && $color->nodeValue !== 'auto') {
                $fontColor = '#' . $color->nodeValue;
            }

            // Font family
            $rFonts = $xpath->query('w:rFonts/@w:ascii', $rPr)->item(0);
            if ($rFonts && !empty($rFonts->nodeValue)) {
                $fontFamily = $rFonts->nodeValue;
            }
        }

        // Process children inside run
        foreach ($rNode->childNodes as $child) {
            if ($child->nodeName === 'w:t') {
                $text .= htmlspecialchars($child->nodeValue);
            } elseif ($child->nodeName === 'w:br') {
                $text .= '<br>';
            } elseif ($child->nodeName === 'w:tab') {
                $text .= '&emsp;&emsp;';
            } elseif ($child->nodeName === 'w:drawing') {
                // Check for embedded images in drawing
                $text .= self::parseDrawing($child, $xpath, $images);
            } elseif ($child->nodeName === 'w:pict') {
                // VML images / Kop Surat shapes
                $text .= self::parsePict($child, $xpath, $images);
            }
        }

        if ($text === '') return '';

        // Apply formatting wrappers
        if ($isBold) $text = "<strong>{$text}</strong>";
        if ($isItalic) $text = "<em>{$text}</em>";
        if ($isUnderline) $text = "<u>{$text}</u>";
        if ($isStrike) $text = "<s>{$text}</s>";

        $inlineStyles = [];
        if ($fontSize) $inlineStyles[] = "font-size: {$fontSize};";
        if ($fontColor) $inlineStyles[] = "color: {$fontColor};";
        if ($fontFamily) $inlineStyles[] = "font-family: '{$fontFamily}', serif;";

        if (!empty($inlineStyles)) {
            $styleAttr = implode(' ', $inlineStyles);
            $text = "<span style=\"{$styleAttr}\">{$text}</span>";
        }

        return $text;
    }

    /**
     * Parse drawing node (<w:drawing>) for Kop Surat and logos
     */
    private static function parseDrawing(\DOMNode $drawingNode, DOMXPath $xpath, array $images = []): string
    {
        // Search for blip embed ID
        $blipNodes = $xpath->query('.//a:blip/@r:embed', $drawingNode);
        if ($blipNodes->length > 0) {
            $embedId = $blipNodes->item(0)->nodeValue;
            if (isset($images[$embedId])) {
                return "<img src=\"{$images[$embedId]}\" alt=\"Kop Surat / Gambar\" style=\"max-width: 100%; height: auto; display: inline-block; margin: 4px 0;\" />";
            }
        }

        return '';
    }

    /**
     * Parse pict node (<w:pict>) for VML images
     */
    private static function parsePict(\DOMNode $pictNode, DOMXPath $xpath, array $images = []): string
    {
        $vImagedata = $xpath->query('.//v:imagedata/@r:id', $pictNode);
        if ($vImagedata->length > 0) {
            $rId = $vImagedata->item(0)->nodeValue;
            if (isset($images[$rId])) {
                return "<img src=\"{$images[$rId]}\" alt=\"Kop Surat / Gambar\" style=\"max-width: 100%; height: auto; display: inline-block; margin: 4px 0;\" />";
            }
        }

        return '';
    }

    /**
     * Parse a table node (<w:tbl>)
     */
    private static function parseTable(\DOMNode $tblNode, DOMXPath $xpath, array $images = []): string
    {
        $tableHtml = '<table style="width: 100%; border-collapse: collapse; margin: 12px 0 16px; font-size: 11pt; line-height: 1.4;">';

        // Check if table has visible borders
        $hasBorders = true;
        $tblBorders = $xpath->query('w:tblPr/w:tblBorders', $tblNode)->item(0);
        if ($tblBorders) {
            $topBdr = $xpath->query('w:top/@w:val', $tblBorders)->item(0);
            if ($topBdr && $topBdr->nodeValue === 'none') {
                $hasBorders = false;
            }
        }

        $rows = $xpath->query('w:tr', $tblNode);
        foreach ($rows as $rowIndex => $tr) {
            $tableHtml .= '<tr>';
            $cells = $xpath->query('w:tc', $tr);

            foreach ($cells as $tc) {
                // Cell width
                $tcW = $xpath->query('w:tcPr/w:tcW/@w:w', $tc)->item(0);
                $widthStyle = '';
                if ($tcW && is_numeric($tcW->nodeValue) && (int)$tcW->nodeValue > 0) {
                    $widthPx = round(((int)$tcW->nodeValue) / 20); // dxa to pt/px approx
                    $widthStyle = "width: {$widthPx}px;";
                }

                // Shading (background color)
                $shd = $xpath->query('w:tcPr/w:shd/@w:fill', $tc)->item(0);
                $bgStyle = '';
                if ($shd && $shd->nodeValue !== 'auto' && !empty($shd->nodeValue)) {
                    $bgStyle = 'background-color: #' . $shd->nodeValue . ';';
                }

                // Cell borders
                $borderStyle = $hasBorders ? 'border: 1px solid #94a3b8;' : 'border: none;';
                $cellStyle = "{$borderStyle} padding: 5px 8px; vertical-align: top; {$widthStyle} {$bgStyle}";

                $cellContent = '';
                foreach ($tc->childNodes as $pChild) {
                    if ($pChild->nodeName === 'w:p') {
                        $cellContent .= self::parseParagraph($pChild, $xpath, $images);
                    }
                }

                $tableHtml .= "<td style=\"{$cellStyle}\">{$cellContent}</td>";
            }

            $tableHtml .= '</tr>';
        }

        $tableHtml .= '</table>';
        return $tableHtml;
    }
}

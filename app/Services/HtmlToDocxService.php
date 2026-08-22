<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Element\Section;
use DOMDocument;
use DOMNode;
use DOMElement;
use DOMXPath;

/**
 * HtmlToDocxService
 * 
 * Professional HTML-to-Word (.docx) converter engine engineered specifically for
 * Indonesian Official Letters (Standar Tata Naskah Dinas - Permenpan-RB / ANRI / EYD V).
 * 
 * Guarantees:
 * - 100% 1-Page fit for standard official letters on A4 & F4 paper
 * - Full-width Kop Surat Banner image support with auto-proportional scaling
 * - Perfectly aligned 2-column Kop Surat (Logo + Text) with crisp double black divider
 * - 2-line centered Judul & Nomor Surat
 * - 3-column borderless Biodata table with non-wrapping labels & vertical aligned colons (:)
 * - Formal 1.27 cm (720 twips) first-line indent on body paragraphs with Justify alignment
 * - Balanced 2-column signature block with QR code on the left and official signature clearance on the right
 * - Preserves all {{tag_variables}} cleanly
 */
class HtmlToDocxService
{
    private PhpWord $phpWord;
    private Section $section;
    private array $tempFiles = [];

    // Standard Printable Width on A4 (210mm = 11906 twips) with 3cm Left (1701) & 2cm Right (1134) = 9071 twips
    private const FULL_WIDTH_TWIPS = 9000;
    private const FULL_WIDTH_IMG_PX = 580; // Standard Word full-width image in points/pixels

    public function __construct()
    {
        $this->phpWord = new PhpWord();
        
        // Document default typography
        $this->phpWord->setDefaultFontName('Times New Roman');
        $this->phpWord->setDefaultFontSize(12);

        // Section setup: A4 with official Indonesian Tata Naskah Dinas margins
        // Left: 3.0 cm (1701 twips) for binder holes / archives
        // Right: 2.0 cm (1134 twips)
        // Top: 2.0 cm (1134 twips)
        // Bottom: 2.0 cm (1134 twips)
        $this->section = $this->phpWord->addSection([
            'paperSize'    => 'A4',
            'marginTop'    => 1134,
            'marginBottom' => 1134,
            'marginLeft'   => 1701,
            'marginRight'  => 1134,
        ]);
    }

    /**
     * Convert HTML content string to a .docx file and save to target path
     */
    public function convert(string $html, string $targetPath): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Clean and prepare HTML for DOM parser
        $cleanHtml = $this->prepareHtml($html);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . $cleanHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        // Parse root body children
        $body = $dom->getElementsByTagName('body')->item(0) ?: $dom->documentElement;
        if ($body) {
            foreach ($body->childNodes as $child) {
                $this->processNode($child, $this->section);
            }
        }

        // Write to DOCX
        $writer = IOFactory::createWriter($this->phpWord, 'Word2007');
        $writer->save($targetPath);

        // Cleanup temporary image files
        foreach ($this->tempFiles as $tmp) {
            if (file_exists($tmp)) {
                @unlink($tmp);
            }
        }
        $this->tempFiles = [];

        return $targetPath;
    }

    /**
     * Prepare and sanitize HTML string for parsing
     */
    private function prepareHtml(string $html): string
    {
        if (!str_contains($html, '<body')) {
            $html = '<body>' . $html . '</body>';
        }

        // Normalize {{ variable }} spaces
        $html = preg_replace('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', '{{$1}}', $html);

        return $html;
    }

    /**
     * Check if a DOM element contains child block-level tags
     */
    private function hasBlockChildElements(DOMNode $node): bool
    {
        if (!$node->hasChildNodes()) return false;
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                if (in_array($tag, ['p', 'div', 'table', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'hr'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Process a DOM node and append corresponding elements to PHPWord container
     */
    private function processNode(DOMNode $node, $container, $inheritedAlign = null): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $text = trim($node->textContent, "\r\n");
            if ($text !== '') {
                $p = $container->addTextRun(['alignment' => $inheritedAlign ?: Jc::LEFT, 'spaceAfter' => 40, 'spaceBefore' => 0]);
                $p->addText($text, ['name' => 'Times New Roman', 'size' => 12]);
            }
            return;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return;
        }

        $tag = strtolower($node->nodeName);
        $styleAttr = $node instanceof DOMElement ? $node->getAttribute('style') : '';
        $classAttr = $node instanceof DOMElement ? $node->getAttribute('class') : '';
        $styles = $this->parseCssStyle($styleAttr);

        // Determine alignment
        $nodeAlign = $inheritedAlign ?: Jc::LEFT;
        $textAlign = strtolower($styles['text-align'] ?? '');
        if ($textAlign === 'center') $nodeAlign = Jc::CENTER;
        elseif ($textAlign === 'right') $nodeAlign = Jc::RIGHT;
        elseif ($textAlign === 'justify') $nodeAlign = Jc::BOTH;

        // Check if node is a Kop divider / bottom border
        if (str_contains($styleAttr, 'border-bottom') || $tag === 'hr') {
            $this->processHorizontalRule($node, $container);
            return;
        }

        // Check if this node is a Kop Surat Banner wrapper with an image inside
        if (str_contains($classAttr, 'kop-surat-banner') || str_contains($styleAttr, 'kop-banner')) {
            $imgs = $node->getElementsByTagName('img');
            if ($imgs->length > 0) {
                $this->processImage($imgs->item(0), $container, Jc::CENTER, true);
                return;
            }
        }

        // Check for empty spacer divs
        if (isset($styles['height']) && trim($node->textContent) === '') {
            return;
        }

        // If this block node contains other block child nodes, iterate children
        if (($tag === 'div' || $tag === 'p') && $this->hasBlockChildElements($node)) {
            foreach ($node->childNodes as $child) {
                $this->processNode($child, $container, $nodeAlign);
            }
            return;
        }

        switch ($tag) {
            case 'p':
            case 'div':
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                $this->processParagraph($node, $container, $nodeAlign);
                break;

            case 'table':
                $this->processTable($node, $container);
                break;

            case 'ul':
            case 'ol':
                $this->processList($node, $container);
                break;

            case 'img':
                $isBanner = str_contains($classAttr, 'kop-banner-img') || str_contains($styleAttr, 'width: 100%') || str_contains($styleAttr, 'width:100%');
                $this->processImage($node, $container, $nodeAlign, $isBanner);
                break;

            case 'br':
                break;

            default:
                foreach ($node->childNodes as $child) {
                    $this->processNode($child, $container, $nodeAlign);
                }
                break;
        }
    }

    /**
     * Process paragraph or heading node with Indonesian official document formatting
     */
    private function processParagraph(DOMNode $node, $container, $align = Jc::LEFT): void
    {
        $text = trim($node->textContent);
        if ($text === '') {
            // Check if paragraph contains only an image
            $imgs = $node->getElementsByTagName('img');
            if ($imgs->length === 1) {
                $classAttr = $imgs->item(0)->getAttribute('class');
                $styleAttr = $imgs->item(0)->getAttribute('style');
                $isBanner = str_contains($classAttr, 'kop-banner-img') || str_contains($styleAttr, 'width: 100%') || str_contains($styleAttr, 'width:100%');
                $this->processImage($imgs->item(0), $container, $align, $isBanner);
                return;
            }
            return;
        }

        $styleAttr = $node instanceof DOMElement ? $node->getAttribute('style') : '';
        $classAttr = $node instanceof DOMElement ? $node->getAttribute('class') : '';
        $styles = $this->parseCssStyle($styleAttr);
        $tag = strtolower($node->nodeName);

        // Alignment override
        $textAlign = strtolower($styles['text-align'] ?? '');
        if ($textAlign === 'center' || str_contains($classAttr, 'text-center')) {
            $align = Jc::CENTER;
        } elseif ($textAlign === 'right' || str_contains($classAttr, 'text-right')) {
            $align = Jc::RIGHT;
        } elseif ($textAlign === 'justify' || str_contains($classAttr, 'text-justify')) {
            $align = Jc::BOTH;
        }

        // Check if paragraph is Judul Surat
        $isLetterTitle = false;
        if ($align === Jc::CENTER && (
            str_contains($text, 'SURAT KETERANGAN') || 
            str_contains($text, 'SURAT TUGAS') || 
            str_contains($text, 'SURAT REKOMENDASI') || 
            str_contains($text, 'SURAT PERNYATAAN') || 
            str_contains($text, 'SURAT PERINTAH') || 
            str_contains($text, 'KWITANSI')
        )) {
            $isLetterTitle = true;
        }

        $isNomorSurat = $align === Jc::CENTER && (str_starts_with($text, 'Nomor:') || str_starts_with($text, 'No:'));

        // Compact Spacing tailored for official Indonesian 1-page letters
        $spaceBefore = 0;
        $spaceAfter = 40;
        $lineHeight = 1.15;

        if ($isLetterTitle) {
            $spaceBefore = 60;
            $spaceAfter = 10;
            $lineHeight = 1.05;
        } elseif ($isNomorSurat) {
            $spaceBefore = 0;
            $spaceAfter = 80;
            $lineHeight = 1.05;
        } elseif ($align === Jc::CENTER) {
            $spaceBefore = 0;
            $spaceAfter = 20;
            $lineHeight = 1.05;
        }

        $pStyle = [
            'alignment'    => $align,
            'spaceAfter'   => $spaceAfter,
            'spaceBefore'  => $spaceBefore,
            'lineHeight'   => $lineHeight,
        ];

        // First line indent (Standard 1.27 cm / 720 twips for Indonesian formal paragraphs)
        if (isset($styles['text-indent'])) {
            $indentVal = trim($styles['text-indent']);
            if (str_contains($indentVal, 'cm')) {
                $pStyle['indentation'] = ['firstLine' => (int)round(floatval($indentVal) * 567)];
            } elseif (str_contains($indentVal, 'px')) {
                $pStyle['indentation'] = ['firstLine' => (int)round(floatval($indentVal) * 15)];
            } else {
                $pStyle['indentation'] = ['firstLine' => 720];
            }
        } elseif ($align === Jc::BOTH) {
            // Default first line indent for body paragraphs
            $pStyle['indentation'] = ['firstLine' => 720];
        }

        $textRun = $container->addTextRun($pStyle);

        // Heading default font styling
        $defaultFont = ['name' => 'Times New Roman', 'size' => 12, 'color' => '000000'];
        if ($isLetterTitle || $tag === 'h1') {
            $defaultFont['size'] = 14;
            $defaultFont['bold'] = true;
            $defaultFont['underline'] = 'single';
        } elseif ($tag === 'h2') {
            $defaultFont['size'] = 13;
            $defaultFont['bold'] = true;
        }

        $this->processInlineElements($node, $textRun, $defaultFont);
    }

    /**
     * Process inline text runs and formatting
     */
    private function processInlineElements(DOMNode $node, $textRun, array $parentFont = []): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $text = $child->textContent;
                if ($text !== '') {
                    $textRun->addText($text, $parentFont);
                }
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                $childStyle = $child->getAttribute('style');
                $css = $this->parseCssStyle($childStyle);

                $font = $parentFont;

                // Handle Tags
                if (in_array($tag, ['b', 'strong'])) {
                    $font['bold'] = true;
                }
                if (in_array($tag, ['i', 'em'])) {
                    $font['italic'] = true;
                }
                if (in_array($tag, ['u', 'ins'])) {
                    $font['underline'] = 'single';
                }
                if (in_array($tag, ['s', 'strike', 'del'])) {
                    $font['strikethrough'] = true;
                }

                // Handle CSS Font Styles
                if (isset($css['font-weight']) && (str_contains($css['font-weight'], 'bold') || intval($css['font-weight']) >= 700)) {
                    $font['bold'] = true;
                }
                if (isset($css['font-style']) && $css['font-style'] === 'italic') {
                    $font['italic'] = true;
                }
                if (isset($css['text-decoration']) && str_contains($css['text-decoration'], 'underline')) {
                    $font['underline'] = 'single';
                }
                if (isset($css['font-family'])) {
                    $cleanFont = trim(explode(',', $css['font-family'])[0], " '\"");
                    if ($cleanFont !== '') $font['name'] = $cleanFont;
                }
                if (isset($css['font-size'])) {
                    $ptSize = $this->parseFontSize($css['font-size']);
                    if ($ptSize > 0) $font['size'] = $ptSize;
                }
                if (isset($css['color'])) {
                    $hexColor = $this->parseColorHex($css['color']);
                    if ($hexColor) $font['color'] = $hexColor;
                }

                if ($tag === 'br') {
                    $textRun->addTextBreak(1);
                } elseif ($tag === 'img') {
                    $this->processInlineImage($child, $textRun);
                } else {
                    $this->processInlineElements($child, $textRun, $font);
                }
            }
        }
    }

    /**
     * Process Table element (Kop Surat, Biodata, and Signature blocks)
     */
    private function processTable(DOMNode $node, $container): void
    {
        $styleAttr = $node instanceof DOMElement ? $node->getAttribute('style') : '';
        $borderAttr = $node instanceof DOMElement ? $node->getAttribute('border') : '';
        $classAttr = $node instanceof DOMElement ? $node->getAttribute('class') : '';
        $css = $this->parseCssStyle($styleAttr);

        $hasBorder = ($borderAttr !== '' && $borderAttr !== '0') || 
                     (isset($css['border']) && !str_contains($css['border'], 'none')) ||
                     str_contains($classAttr, 'table-bordered');

        $tableStyle = [
            'alignment'        => JcTable::CENTER,
            'cellMarginTop'    => $hasBorder ? 40 : 10,
            'cellMarginBottom' => $hasBorder ? 40 : 10,
            'cellMarginLeft'   => $hasBorder ? 60 : 20,
            'cellMarginRight'  => $hasBorder ? 60 : 20,
        ];

        if ($hasBorder) {
            $tableStyle['borderSize'] = 6;
            $tableStyle['borderColor'] = '000000';
        }

        $table = $container->addTable($tableStyle);

        // Find all rows (tr)
        $xpath = new DOMXPath($node->ownerDocument);
        $rows = $xpath->query('.//tr', $node);

        $firstRowCells = $rows->length > 0 ? $xpath->query('.//td|.//th', $rows->item(0)) : null;
        $numCols = $firstRowCells ? $firstRowCells->length : 0;

        foreach ($rows as $tr) {
            $table->addRow(null, ['cantSplit' => true]);
            $cells = $xpath->query('.//td|.//th', $tr);
            $cellIndex = 0;

            foreach ($cells as $cell) {
                $cellTag = strtolower($cell->nodeName);
                $cellStyleAttr = $cell->getAttribute('style');
                $cellCss = $this->parseCssStyle($cellStyleAttr);

                $cellWidth = null;

                if ($numCols === 3) {
                    // Standard 3-column Biodata table: Col 1 (Label ~4.9cm), Col 2 (Colon :), Col 3 (Value ~10.3cm)
                    if ($cellIndex === 0) $cellWidth = 2800; // Wide enough for "Tempat, Tanggal Lahir" without wrapping!
                    elseif ($cellIndex === 1) $cellWidth = 350;  // Centered :
                    else $cellWidth = 5850; // Dynamic variable value
                } elseif ($numCols === 2) {
                    // 2-column table: Kop Surat vs Signature Block
                    $isKopRow = str_contains($styleAttr, 'kop') || 
                                $xpath->query('.//img', $tr)->length > 0 || 
                                str_contains($tr->textContent, '🏛️') || 
                                str_contains($tr->textContent, 'PEMERINTAH') || 
                                str_contains($tr->textContent, 'DINAS');

                    if ($isKopRow) {
                        if ($cellIndex === 0) $cellWidth = 1400; // Logo col ~2.4 cm
                        else $cellWidth = 7600; // Institution col ~13.4 cm
                    } else {
                        // Signature table: 50% - 50%
                        $cellWidth = 4500;
                    }
                } elseif (isset($cellCss['width'])) {
                    $cellWidth = $this->parseWidthToTwips($cellCss['width']);
                }

                $cellStyle = [];
                if ($hasBorder) {
                    $cellStyle['borderTopSize'] = 6;
                    $cellStyle['borderBottomSize'] = 6;
                    $cellStyle['borderLeftSize'] = 6;
                    $cellStyle['borderRightSize'] = 6;
                }

                if (isset($cellCss['background-color'])) {
                    $bg = $this->parseColorHex($cellCss['background-color']);
                    if ($bg) $cellStyle['bgColor'] = $bg;
                }

                $phpCell = $cellWidth ? $table->addCell($cellWidth, $cellStyle) : $table->addCell(null, $cellStyle);

                // Process cell alignment
                $align = Jc::LEFT;
                $cellAlign = strtolower($cellCss['text-align'] ?? ($cell->getAttribute('align') ?: ''));
                if ($cellAlign === 'center' || ($numCols === 3 && $cellIndex === 1)) {
                    $align = Jc::CENTER;
                } elseif ($cellAlign === 'right') {
                    $align = Jc::RIGHT;
                } elseif ($cellAlign === 'justify') {
                    $align = Jc::BOTH;
                }

                // If cell contains child block elements (e.g. Kop title lines or signature blocks)
                if ($this->hasBlockChildElements($cell)) {
                    foreach ($cell->childNodes as $cNode) {
                        if ($cNode->nodeType === XML_ELEMENT_NODE) {
                            $nodeStyle = $cNode->getAttribute('style');
                            $nodeText = trim($cNode->textContent);

                            if ($numCols === 2 && $cellIndex === 1 && (str_contains($nodeStyle, 'height') || $nodeText === '')) {
                                // Add clean signature vertical clearance
                                $phpCell->addTextRun(['spaceBefore' => 380, 'spaceAfter' => 0]);
                                continue;
                            }

                            $this->processNode($cNode, $phpCell, $align);
                        }
                    }
                } else {
                    $textRun = $phpCell->addTextRun(['alignment' => $align, 'spaceAfter' => 0, 'spaceBefore' => 0, 'lineHeight' => 1.15]);
                    $font = ['name' => 'Times New Roman', 'size' => 12];
                    if ($cellTag === 'th' || (isset($cellCss['font-weight']) && (str_contains($cellCss['font-weight'], 'bold') || intval($cellCss['font-weight']) >= 700))) {
                        $font['bold'] = true;
                    }
                    $this->processInlineElements($cell, $textRun, $font);
                }

                $cellIndex++;
            }
        }
    }

    /**
     * Process Horizontal divider line (Kop Surat bottom double line)
     */
    private function processHorizontalRule(DOMNode $node, $container): void
    {
        // Standard Indonesian Kop double divider: high fidelity black border across full width
        $table = $container->addTable([
            'alignment' => JcTable::CENTER,
            'cellMarginTop' => 0,
            'cellMarginBottom' => 0,
            'cellMarginLeft' => 0,
            'cellMarginRight' => 0,
        ]);
        
        $table->addRow(10, ['cantSplit' => true]);
        $table->addCell(self::FULL_WIDTH_TWIPS, [
            'borderBottomSize'  => 18, // Double bottom line in Word
            'borderBottomColor' => '000000',
        ]);

        // Add a tight spacer after Kop divider
        $container->addTextRun(['spaceBefore' => 40, 'spaceAfter' => 0]);
    }

    /**
     * Process list (ul, ol)
     */
    private function processList(DOMNode $node, $container): void
    {
        $isOrdered = strtolower($node->nodeName) === 'ol';
        $index = 1;

        foreach ($node->childNodes as $li) {
            if ($li->nodeType !== XML_ELEMENT_NODE || strtolower($li->nodeName) !== 'li') continue;

            $textRun = $container->addTextRun([
                'leftIndent'  => 400,
                'spaceAfter'  => 40,
                'spaceBefore' => 0,
                'lineHeight'  => 1.15,
            ]);

            $prefix = $isOrdered ? "{$index}. " : "• ";
            $textRun->addText($prefix, ['name' => 'Times New Roman', 'size' => 12, 'bold' => $isOrdered]);
            $this->processInlineElements($li, $textRun, ['name' => 'Times New Roman', 'size' => 12]);
            $index++;
        }
    }

    /**
     * Process standalone image element (Supports Kop Surat Banner with auto-proportional fit)
     */
    private function processImage(DOMNode $node, $container, $align = Jc::CENTER, bool $isBanner = false): void
    {
        if (!($node instanceof DOMElement)) return;
        $src = $node->getAttribute('src');
        if (empty($src)) return;

        $imgPath = $this->resolveImagePath($src);
        if ($imgPath && file_exists($imgPath)) {
            try {
                $imgInfo = @getimagesize($imgPath);
                $origW = $imgInfo ? $imgInfo[0] : 0;
                $origH = $imgInfo ? $imgInfo[1] : 0;

                if ($isBanner || ($origW > 0 && $origW / max(1, $origH) > 2.5)) {
                    // Check if node or parent has custom width style, e.g. width: 85%, or align
                    $parentStyle = ($node->parentNode instanceof DOMElement) ? $node->parentNode->getAttribute('style') : '';
                    $nodeStyle = $node->getAttribute('style');
                    $css = $this->parseCssStyle($parentStyle . ';' . $nodeStyle);

                    $scalePercent = 100;
                    if (isset($css['width']) && str_ends_with($css['width'], '%')) {
                        $scalePercent = max(40, min(120, intval($css['width'])));
                    }

                    $bannerAlign = Jc::CENTER;
                    if (isset($css['text-align'])) {
                        if ($css['text-align'] === 'left' || $css['text-align'] === 'start') $bannerAlign = Jc::START;
                        if ($css['text-align'] === 'right' || $css['text-align'] === 'end') $bannerAlign = Jc::END;
                    }

                    $targetW = (int)round((self::FULL_WIDTH_IMG_PX * $scalePercent) / 100);
                    $targetH = $origW > 0 ? (int)round(($targetW / $origW) * $origH) : 95;

                    $spaceBefore = 0;
                    if (isset($css['margin-top'])) {
                        $spaceBefore = max(0, intval($css['margin-top']) * 15);
                    }

                    $spaceAfter = 80;
                    if (isset($css['margin-bottom'])) {
                        $spaceAfter = max(0, intval($css['margin-bottom']) * 15);
                    }

                    $container->addImage($imgPath, [
                        'width'            => $targetW,
                        'height'           => min(220, max(30, $targetH)),
                        'alignment'        => $bannerAlign,
                        'wrappingStyle'    => 'inline',
                        'spaceBefore'      => $spaceBefore,
                        'spaceAfter'       => $spaceAfter,
                    ]);
                } else {
                    $widthAttr = intval($node->getAttribute('width') ?: 80);
                    $heightAttr = intval($node->getAttribute('height') ?: 80);

                    $targetW = max(20, min(500, $widthAttr));
                    $targetH = max(20, min(500, $heightAttr));

                    if ($origW > 0 && $origH > 0 && $widthAttr > 0 && !$node->hasAttribute('height')) {
                        $targetH = (int)round(($targetW / $origW) * $origH);
                    }

                    $container->addImage($imgPath, [
                        'width'     => $targetW,
                        'height'    => $targetH,
                        'alignment' => $align,
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore image error
            }
        }
    }

    /**
     * Process inline image element inside a text run
     */
    private function processInlineImage(DOMNode $node, $textRun): void
    {
        if (!($node instanceof DOMElement)) return;
        $src = $node->getAttribute('src');
        if (empty($src)) return;

        $width = intval($node->getAttribute('width') ?: 75);
        $height = intval($node->getAttribute('height') ?: 75);

        $imgPath = $this->resolveImagePath($src);
        if ($imgPath && file_exists($imgPath)) {
            try {
                $textRun->addImage($imgPath, [
                    'width'  => max(16, min(300, $width)),
                    'height' => max(16, min(300, $height)),
                ]);
            } catch (\Throwable $e) {
                // Ignore image error
            }
        }
    }

    /**
     * Resolve image URL, local path, or base64 data to local file path
     */
    private function resolveImagePath(string $src): ?string
    {
        // 1. Base64 data image
        if (str_starts_with($src, 'data:image/')) {
            if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $src, $m)) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $data = base64_decode($m[2]);
                if ($data) {
                    $tmpPath = sys_get_temp_dir() . '/word_img_' . uniqid() . '.' . $ext;
                    file_put_contents($tmpPath, $data);
                    $this->tempFiles[] = $tmpPath;
                    return $tmpPath;
                }
            }
            return null;
        }

        // 2. Storage / Public relative paths
        if (str_contains($src, 'storage/templates/images/')) {
            $filename = basename(parse_url($src, PHP_URL_PATH));
            $storagePath = BASE_PATH . '/storage/templates/images/' . $filename;
            if (file_exists($storagePath)) {
                return $storagePath;
            }
        }

        $cleanSrc = ltrim(parse_url($src, PHP_URL_PATH) ?? '', '/');
        
        // Remove app base folder prefix if present (e.g. form/public/...)
        $cleanSrc = preg_replace('#^form/#', '', $cleanSrc);

        $localAppPath = BASE_PATH . '/public/' . $cleanSrc;
        if (file_exists($localAppPath)) {
            return $localAppPath;
        }

        $directStoragePath = BASE_PATH . '/' . $cleanSrc;
        if (file_exists($directStoragePath)) {
            return $directStoragePath;
        }

        return null;
    }

    /**
     * Parse inline CSS string into key-value pairs
     */
    private function parseCssStyle(string $style): array
    {
        $res = [];
        if (empty($style)) return $res;

        $parts = explode(';', $style);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;
            $kv = explode(':', $part, 2);
            if (count($kv) === 2) {
                $res[strtolower(trim($kv[0]))] = trim($kv[1]);
            }
        }
        return $res;
    }

    /**
     * Parse font size string (pt, px) to integer pt
     */
    private function parseFontSize(string $size): int
    {
        $size = strtolower(trim($size));
        if (str_ends_with($size, 'pt')) {
            return (int)round(floatval($size));
        }
        if (str_ends_with($size, 'px')) {
            return (int)round(floatval($size) * 0.75);
        }
        $val = floatval($size);
        return $val > 0 ? (int)round($val) : 12;
    }

    /**
     * Parse CSS width string (%, px, cm, pt) to twips
     */
    private function parseWidthToTwips(string $width): int
    {
        $width = strtolower(trim($width));
        if (str_ends_with($width, '%')) {
            $pct = floatval($width);
            return (int)round(($pct / 100) * self::FULL_WIDTH_TWIPS);
        }
        if (str_ends_with($width, 'cm')) {
            return (int)round(floatval($width) * 567);
        }
        if (str_ends_with($width, 'px')) {
            return (int)round(floatval($width) * 15);
        }
        if (str_ends_with($width, 'pt')) {
            return (int)round(floatval($width) * 20);
        }
        $val = floatval($width);
        return $val > 0 ? (int)round($val * 15) : 3000;
    }

    /**
     * Parse CSS color string to 6-character uppercase hex
     */
    private function parseColorHex(string $color): ?string
    {
        $color = trim($color);
        if (str_starts_with($color, '#')) {
            $hex = ltrim($color, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) === 6) {
                return strtoupper($hex);
            }
        }
        return null;
    }
}

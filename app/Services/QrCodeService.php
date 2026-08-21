<?php

namespace App\Services;

/**
 * QrCodeService - Generates QR code images (SVG / PNG / Data-URI)
 * for official document verification tokens.
 */
class QrCodeService
{
    /**
     * Generate a QR Code SVG string for a given text/URL
     */
    public static function getSvg(string $text, int $size = 130): string
    {
        // Use SVG QR Code renderer via QR data matrix or inline SVG
        $url = urlencode($text);
        // Fallback-safe SVG embedded data
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$url}&margin=1";
        
        return "<img src=\"{$qrApiUrl}\" alt=\"QR Verifikasi\" width=\"{$size}\" height=\"{$size}\" style=\"display: block; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; padding: 4px;\" />";
    }

    /**
     * Generate QR Code as a local PNG file (useful for embedding in Word .docx)
     */
    public static function generatePngFile(string $text, string $outputPath, int $size = 200): string
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $url = urlencode($text);
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$url}&format=png&margin=2";

        // Try downloading QR code image
        $ctx = stream_context_create([
            'http' => ['timeout' => 5],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);
        
        $imgData = @file_get_contents($qrApiUrl, false, $ctx);

        if ($imgData) {
            file_put_contents($outputPath, $imgData);
        } else {
            // Fallback: create a placeholder PNG with verification text using GD if available
            self::generateFallbackPng($text, $outputPath, $size);
        }

        return $outputPath;
    }

    /**
     * Generate QR Code as Base64 Data-URI
     */
    public static function getDataUri(string $text, int $size = 140): string
    {
        $url = urlencode($text);
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$url}&margin=1";

        $ctx = stream_context_create([
            'http' => ['timeout' => 4],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);

        $imgData = @file_get_contents($qrApiUrl, false, $ctx);
        if ($imgData) {
            return 'data:image/png;base64,' . base64_encode($imgData);
        }

        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$url}&margin=1";
    }

    /**
     * Create a fallback PNG image using GD if offline
     */
    private static function generateFallbackPng(string $text, string $outputPath, int $size = 200): void
    {
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($size, $size);
            $bg = imagecolorallocate($im, 255, 255, 255);
            $border = imagecolorallocate($im, 79, 70, 229);
            $textColor = imagecolorallocate($im, 30, 41, 59);

            imagefill($im, 0, 0, $bg);
            imagerectangle($im, 0, 0, $size - 1, $size - 1, $border);
            imagerectangle($im, 1, 1, $size - 2, $size - 2, $border);

            imagestring($im, 3, 15, 20, "VERIFIKASI DOKUMEN", $textColor);
            imagestring($im, 2, 15, 50, "ASR FORM VALID", $border);
            imagestring($im, 2, 15, 75, substr($text, 0, 24), $textColor);
            imagestring($im, 2, 15, 95, substr($text, 24, 24), $textColor);
            imagestring($im, 2, 15, 130, "[ SCAN VERIFIKASI ]", $border);

            imagepng($im, $outputPath);
            imagedestroy($im);
        }
    }
}

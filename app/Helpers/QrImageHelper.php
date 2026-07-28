<?php

namespace App\Helpers;

/**
 * Builds branded QR Code card images using PHP GD.
 *
 * Layout (829 × 1115 px):
 *  ┌──────────────────────────────┐
 *  │   solid blue header (~270px) │  ← SwiftPay logo (Logo-RGB-02-JPG)
 *  ├──────────────────────────────┤
 *  │   teal accent bar (8 px)     │
 *  ├──────────────────────────────┤
 *  │   solid blue body            │  ← QR overlay + plate text
 *  └──────────────────────────────┘
 */
class QrImageHelper
{
    // Canvas size (matches original background)
    private const W = 829;
    private const H = 1115;

    // Brand colours (RGB)
    private const C_DEEP  = [15,  26,  59];   // #0F1A3B (matches Logo-RGB-02-JPG background)
    private const C_TEAL  = [44, 155, 165];   // #2C9BA5
    private const C_WHITE = [255, 255, 255];

    // Layout heights
    private const HEADER_H = 270;   // logo area
    private const ACCENT_H = 8;     // teal stripe

    /**
     * Composite a QR-code base64 image onto a branded background.
     *
     * @param  string $qrBase64   The raw base64 string (no data-URL prefix) from the API.
     * @param  string $plateText  Text to render at the bottom (e.g. machine plate ID).
     * @param  string $fontPath   Absolute path to a TTF font file (optional).
     * @return string             data:image/png;base64,… ready for <img src="…">
     */
    public static function buildQrImage(string $qrBase64, string $plateText, string $fontPath = ''): string
    {
        // ── Background ──────────────────────────────────────────────────────────
        $bg = self::createBackground();

        // ── QR overlay ──────────────────────────────────────────────────────────
        $overlay = imagecreatefromstring(base64_decode($qrBase64));

        $origW = imagesx($overlay);
        $origH = imagesy($overlay);
        $scale = 1.4;
        $newW  = (int) ($origW * $scale);
        $newH  = (int) ($origH * $scale);

        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $overlay, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        // Centre the QR horizontally; push it down into the dark section
        $qrX = (int) ((self::W - $newW) / 2);
        $qrY = (int) ((self::H - $newH) / 2) + 150;

        imagecopy($bg, $resized, $qrX, $qrY, 0, 0, $newW, $newH);
        imagedestroy($resized);
        imagedestroy($overlay);

        // ── Plate text ──────────────────────────────────────────────────────────
        self::drawPlateText($bg, $plateText, $fontPath);

        // ── Encode ──────────────────────────────────────────────────────────────
        ob_start();
        imagepng($bg);
        $raw = ob_get_clean();
        imagedestroy($bg);

        return 'data:image/png;base64,' . base64_encode($raw);
    }

    /**
     * Creates the branded background canvas (GD image resource).
     * Caller is responsible for imagedestroy().
     */
    private static function createBackground()
    {
        $img = imagecreatetruecolor(self::W, self::H);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // ── Solid blue background (full canvas) ─────────────────────────────────
        $deep = imagecolorallocate($img, ...self::C_DEEP);
        imagefilledrectangle($img, 0, 0, self::W - 1, self::H - 1, $deep);

        // ── Teal accent bar ───────────────────────────────────────────────────────
        $teal = imagecolorallocate($img, ...self::C_TEAL);
        imagefilledrectangle(
            $img,
            0,
            self::HEADER_H,
            self::W - 1,
            self::HEADER_H + self::ACCENT_H - 1,
            $teal
        );

        // ── SwiftPay logo (white/teal artwork, already on brand-blue background) ──
        $logoPath = public_path('site/img/Logo-RGB-02-JPG.jpg');
        if (is_file($logoPath)) {
            $logo = imagecreatefromjpeg($logoPath);
            if ($logo) {
                // Trim a few px off each edge to drop the JPEG edge-bleed border
                $crop   = 6;
                $lOrigW = imagesx($logo) - ($crop * 2);
                $lOrigH = imagesy($logo) - ($crop * 2);

                // Scale to fit 580 px wide inside the header
                $targetW = 580;
                $targetH = (int) ($lOrigH * ($targetW / $lOrigW));

                // Don't exceed header height with some padding
                $maxH = self::HEADER_H - 40;
                if ($targetH > $maxH) {
                    $targetH = $maxH;
                    $targetW = (int) ($lOrigW * ($targetH / $lOrigH));
                }

                $scaledLogo = imagecreatetruecolor($targetW, $targetH);
                imagecopyresampled($scaledLogo, $logo, 0, 0, $crop, $crop, $targetW, $targetH, $lOrigW, $lOrigH);

                // Centre in header
                $lx = (int) ((self::W - $targetW) / 2);
                $ly = (int) ((self::HEADER_H - $targetH) / 2);

                imagecopy($img, $scaledLogo, $lx, $ly, 0, 0, $targetW, $targetH);
                imagedestroy($scaledLogo);
                imagedestroy($logo);
            }
        }

        return $img;
    }

    /**
     * Draws the machine plate text at the bottom of the image in white.
     */
    private static function drawPlateText($img, string $text, string $fontPath): void
    {
        $white = imagecolorallocate($img, ...self::C_WHITE);

        if ($fontPath && is_file($fontPath)) {
            // ~44px native ≈ 14pt when the card is previewed at its usual 350px width on screen.
            $fontSize = 44;
            $angle    = 0;
            $bbox     = imagettfbbox($fontSize, $angle, $fontPath, $text);
            $minX     = min($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
            $maxX     = max($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
            $maxY     = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $textW    = $maxX - $minX;
            $textX    = (int) (((self::W - $textW) / 2) - $minX);
            $textY    = (int) (self::H - 20 - $maxY);
            imagettftext($img, $fontSize, $angle, $textX, $textY, $white, $fontPath, $text);
        } else {
            $font    = 5;
            $textW   = imagefontwidth($font) * strlen($text);
            $textH   = imagefontheight($font);
            $textX   = (int) ((self::W - $textW) / 2);
            $textY   = (int) (self::H - $textH - 20);
            imagestring($img, $font, $textX, $textY, $text, $white);
        }
    }
}

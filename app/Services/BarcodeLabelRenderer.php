<?php

namespace App\Services;

class BarcodeLabelRenderer
{
    private int $pxPerMm = 10;

    public function renderRow(array $labels, array $labelSize, object $settings): string
    {
        $labelW = (int) $labelSize['width'] * $this->pxPerMm;
        $labelH = (int) $labelSize['height'] * $this->pxPerMm;
        $cols = max(1, (int) $labelSize['cols']);

        $row = imagecreatetruecolor($labelW * $cols, $labelH);
        $white = imagecolorallocate($row, 255, 255, 255);
        imagefill($row, 0, 0, $white);

        foreach ($labels as $index => $label) {
            if ($index >= $cols) {
                break;
            }
            $sticker = $this->renderLabel($label, $labelSize, $settings);
            imagecopy($row, $sticker, $index * $labelW, 0, 0, 0, $labelW, $labelH);
            imagedestroy($sticker);
        }

        ob_start();
        imagepng($row);
        $png = ob_get_clean();
        imagedestroy($row);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    public function renderSplitRow(array $labels, array $labelSize, object $settings): string
    {
        $labelW = (int) $labelSize['width'] * $this->pxPerMm;
        $labelH = (int) $labelSize['height'] * $this->pxPerMm;
        $cols = max(1, (int) $labelSize['cols']);

        $row = imagecreatetruecolor($labelW * $cols, $labelH);
        $white = imagecolorallocate($row, 255, 255, 255);
        imagefill($row, 0, 0, $white);

        foreach ($labels as $index => $label) {
            if ($index >= $cols) {
                break;
            }
            $sticker = $this->renderSplitLabel($label, $labelSize, $settings);
            imagecopy($row, $sticker, $index * $labelW, 0, 0, 0, $labelW, $labelH);
            imagedestroy($sticker);
        }

        ob_start();
        imagepng($row);
        $png = ob_get_clean();
        imagedestroy($row);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    public function renderSplitLabel(array $label, array $labelSize, object $settings): \GdImage
    {
        $w = (int) $labelSize['width'] * $this->pxPerMm;
        $h = (int) $labelSize['height'] * $this->pxPerMm;
        $tall = !empty($labelSize['tall']);

        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);
        if (function_exists('imageantialias')) {
            @imageantialias($img, true);
        }
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        $bold = $this->fontPath(true);

        $padTop = $this->mmToPx($tall ? 1.1 : 0.8);
        $padRight = $this->mmToPx($tall ? 2.4 : 2.6);
        $padBottom = $this->mmToPx($tall ? 1.0 : 0.8);
        $padLeft = $this->mmToPx($tall ? 1.1 : 1.0);
        $contentW = max(1, $w - $padLeft - $padRight);

        $includeTitle = !array_key_exists('include_title', $labelSize) || !empty($labelSize['include_title']);
        $company = strtoupper(trim((string) ($settings->name ?? 'POOJA SAMAN.IN')));
        $address = trim((string) ($settings->address ?? 'DURG,RISALI,RAIPUR'));
        $address = preg_replace('/\s*,\s*/', ', ', preg_replace('/\s+/', ' ', $address));
        $address = strtoupper($address);
        $name = strtoupper(trim((string) ($label['name'] ?? '')));
        $labelCode = strtoupper(trim((string) ($label['label_code'] ?? '')));
        $hideMrpRate = ! empty($label['hide_mrp_rate']);
        $mrp = $hideMrpRate ? '' : $this->formatNumber($label['mrp'] ?? null);
        $rate = $hideMrpRate ? '' : $this->formatNumber($label['price'] ?? null);
        // Always keep MRP-/RATE- labels; omit_mrp_rate only clears the numbers.
        // Empty values leave write-in space for pen.
        $blankPad = '______';
        $mrpLabel = 'MRP-' . ($mrp !== '' ? $mrp : $blankPad);
        $rateLabel = 'RATE-' . ($rate !== '' ? $rate : $blankPad);
        $barcode = (string) ($label['barcode_display'] ?? $label['barcode'] ?? '');
        $qty = trim((string) ($label['specific_qty'] ?? ''));
        $batch = trim((string) ($label['batch_no'] ?? ''));
        $showWholesale = ! empty($label['show_wholesale_on_barcode']);
        $ref = \App\Models\Product::formatBarcodeWholesaleRef((string) ($label['label_reference'] ?? ''), $showWholesale);

        $companySize = $tall ? 11.5 : 10.5;
        $addressSize = $tall ? 7.8 : 6.8;
        $productSize = $tall ? 8.2 : 6.8;
        $codeSize = $tall ? 8.2 : 6.8;
        $mrpSize = $tall ? 7.4 : 6.1;
        $rateSize = $tall ? 7.6 : 6.3;
        $metaSize = $tall ? 6.2 : 5.2;

        $addressGap = $this->mmToPx(0.2);
        $productGap = $this->mmToPx(0.45);
        $codeGap = $this->mmToPx(0.1);
        $bottomGap = $this->mmToPx(0.35);

        $cursorY = $padTop;

        if ($includeTitle) {
            $cursorY += $this->fontAscent($bold, $companySize);
            $this->centerText($img, $bold, $companySize, $company, $w, $cursorY, $black, $contentW);
            $cursorY += $this->fontHeight($bold, $companySize) + $addressGap;
            $cursorY += $this->fontAscent($bold, $addressSize);
            $this->centerText($img, $bold, $addressSize, $address, $w, $cursorY, $black, $contentW);
            $cursorY += $this->fontHeight($bold, $addressSize);
        }

        $cursorY += $productGap + $this->fontAscent($bold, $productSize);
        $this->centerText($img, $bold, $productSize, $name, $w, $cursorY, $black, $contentW);
        $cursorY += $this->fontHeight($bold, $productSize);

        if ($labelCode !== '') {
            $cursorY += $codeGap + $this->fontAscent($bold, $codeSize);
            $this->centerText($img, $bold, $codeSize, $labelCode, $w, $cursorY, $black, $contentW);
            $cursorY += $this->fontHeight($bold, $codeSize);
        }

        $bottomTop = $cursorY + $bottomGap;
        $bottomHeight = max(1, $h - $padBottom - $bottomTop);

        $bcColW = (int) round($contentW * 0.62);
        $priceColW = max(1, $contentW - $bcColW);
        $bcX = $padLeft;
        $priceX = $padLeft + $bcColW + $this->mmToPx(0.5);
        $priceMaxW = max(1, $w - $priceX - $padRight);

        $bcWrapH = min($this->mmToPx($tall ? 12.0 : 8.2), max(1, $bottomHeight - $this->fontHeight($bold, $metaSize) - 2));
        $bcNumH = $this->fontHeight($bold, $metaSize);
        $pricesH = $this->fontHeight($bold, $mrpSize) + 1 + $this->fontHeight($bold, $rateSize);

        $leftBlockH = $bcWrapH + 2 + $bcNumH;
        $blockH = max($leftBlockH, $pricesH);
        $blockTop = $bottomTop + (int) max(0, ($bottomHeight - $blockH) / 2);

        $barcodeSrc = $label['svg'] ?? $label['img'] ?? '';
        if ($barcodeSrc !== '') {
            $this->drawBarcode($img, $barcodeSrc, $bcX, $blockTop, $bcColW - $this->mmToPx(0.6), $bcWrapH);
        }

        $numY = $blockTop + $bcWrapH + 2 + $this->fontAscent($bold, $metaSize);
        $footerMeta = trim(implode('  ', array_filter([$qty, $batch, $ref])));
        $barcodeLine = trim($barcode . ($footerMeta !== '' ? '  ' . $footerMeta : ''));
        $this->centerTextInBox($img, $bold, $metaSize, $barcodeLine, $bcX, $bcX + $bcColW, $numY, $black);

        $priceY = $blockTop + $this->fontAscent($bold, $mrpSize) + (int) max(0, ($leftBlockH - $pricesH) / 2);
        $this->text($img, $bold, $mrpSize, $mrpLabel, $priceX, $priceY, $black, $priceMaxW);
        $priceY += $this->fontHeight($bold, $mrpSize) + 1;
        $this->text($img, $bold, $rateSize, $rateLabel, $priceX, $priceY, $black, $priceMaxW);

        return $img;
    }

    private function mmToPx(float $mm): int
    {
        return (int) round($mm * $this->pxPerMm);
    }

    public function renderLabel(array $label, array $labelSize, object $settings): \GdImage
    {
        $w = (int) $labelSize['width'] * $this->pxPerMm;
        $h = (int) $labelSize['height'] * $this->pxPerMm;
        $tall = !empty($labelSize['tall']);

        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);
        if (function_exists('imageantialias')) {
            @imageantialias($img, false);
        }
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        $pad = $tall ? 10 : 8;

        $regular = $this->fontPath(false);
        $bold = $this->fontPath(true);

        $company = (string) ($settings->name ?? 'POOJA SAMAN.IN');
        $address = (string) ($settings->address ?? 'DURG,RISALI,RAIPUR');
        $includeTitle = !array_key_exists('include_title', $labelSize) || !empty($labelSize['include_title']);
        $name = strtoupper((string) ($label['name'] ?? ''));
        $code = (string) ($label['label_code'] ?? '');
        $hideMrpRate = ! empty($label['hide_mrp_rate']);
        $mrp = $hideMrpRate ? '' : $this->formatNumber($label['mrp'] ?? null);
        $rate = $hideMrpRate ? '' : $this->formatNumber($label['price'] ?? null);
        // Always keep labels; empty numbers leave write-in space → "MRP-______  RATE-______"
        $blankPad = '______';
        $mrpPart = 'MRP-' . ($mrp !== '' ? $mrp : $blankPad) . '  ';
        $ratePart = 'RATE-' . ($rate !== '' ? $rate : $blankPad);
        $barcode = (string) ($label['barcode_display'] ?? $label['barcode'] ?? '');
        $qty = (string) ($label['specific_qty'] ?? '');
        $batch = (string) ($label['batch_no'] ?? '');
        $showWholesale = ! empty($label['show_wholesale_on_barcode']);
        $ref = \App\Models\Product::formatBarcodeWholesaleRef((string) ($label['label_reference'] ?? ''), $showWholesale);

        // Compact sizes so company + product + code + price + barcode + footer fit in 25mm.
        $companySize = $tall ? 12.0 : 10.0;
        $addressSize = $tall ? 9.0 : 7.5;
        $nameSize = $tall ? 11.0 : 9.5;
        $codeSize = $tall ? 11.0 : 9.5;
        $mrpSize = $tall ? 11.0 : 9.5;
        $rateSize = $tall ? 12.0 : 10.0;
        $priceSize = $rateSize;
        $metaSize = $tall ? 9.0 : 8.0;
        $qtySize = $tall ? 13.0 : 10.0;
        $lineGap = $tall ? 3 : 1;
        $barcodeH = $tall ? (int) ($h * 0.24) : (int) ($h * 0.22);
        $footerH = $this->fontHeight($bold, $metaSize) + (($batch !== '' && $ref !== '') ? (int) ($metaSize + 3) : 0);

        $hasPriceLine = true;
        $blockH = ($includeTitle ? ($this->fontHeight($bold, $companySize) + $lineGap + $this->fontHeight($regular, $addressSize) + $lineGap) : 0)
            + $this->fontHeight($bold, $nameSize)
            + $lineGap
            + ($code !== '' ? $this->fontHeight($bold, $codeSize) + $lineGap : 0)
            + ($hasPriceLine ? $this->fontHeight($bold, $priceSize) : 0)
            + ($tall ? 6 : 3)
            + $barcodeH
            + ($tall ? 6 : 3)
            + $footerH;

        $topSafe = $tall ? 14 : 10;
        $bottomSafe = $tall ? 10 : 8;
        $available = max(1, $h - $topSafe - $bottomSafe);
        if ($blockH > $available) {
            $scale = $available / $blockH;
            $companySize *= $scale;
            $addressSize *= $scale;
            $nameSize *= $scale;
            $codeSize *= $scale;
            $mrpSize *= $scale;
            $rateSize *= $scale;
            $priceSize *= $scale;
            $metaSize *= $scale;
            $qtySize *= $scale;
            $barcodeH = max(18, (int) ($barcodeH * $scale));
            $blockH = $available;
        }
        $startY = $topSafe + max(0, (int) (($available - $blockH) / 2));

        $maxText = $w - ($pad * 2);
        $y = $startY + $this->fontAscent($bold, $includeTitle ? $companySize : $nameSize);

        if ($includeTitle) {
            $this->centerText($img, $bold, $companySize, $company, $w, $y, $black, $maxText);
            $y += $this->fontHeight($regular, $addressSize) + $lineGap + 2;
            $this->centerText($img, $bold, $addressSize, $address, $w, $y, $black, $maxText);
            $y += $this->fontHeight($bold, $nameSize) + $lineGap + 2;
        }
        $this->centerText($img, $bold, $nameSize, $name, $w, $y, $black, $maxText);
        if ($code !== '') {
            $y += $this->fontHeight($bold, $codeSize) + $lineGap;
            $this->centerText($img, $bold, $codeSize, $code, $w, $y, $black, $maxText);
        }
        $y += $this->fontHeight($bold, $rateSize) + $lineGap;
        $this->centerPriceLine($img, $bold, $mrpSize, $mrpPart, $rateSize, $ratePart, $w, $y, $black, $maxText);
        $y += ($tall ? 6 : 3);

        $barcodeW = (int) ($w * ($qty !== '' ? 0.70 : 0.90));
        $barcodeX = (int) (($w - $barcodeW) / 2) - ($qty !== '' ? (int) ($w * 0.08) : 0);

        if (!empty($label['img']) || !empty($label['svg'])) {
            $this->drawBarcode($img, $label['svg'] ?? $label['img'], $barcodeX, $y, $barcodeW, $barcodeH);
        }

        if ($qty !== '') {
            $this->centerTextInBox($img, $bold, $qtySize, $qty, $barcodeX + $barcodeW, $w - $pad, $y + (int) ($barcodeH / 2) + 4, $black);
        }

        $y += $barcodeH + ($tall ? 6 : 3) + $this->fontAscent($bold, $metaSize);
        // Keep footer above bottom edge of sticker.
        $maxY = $h - $bottomSafe;
        if ($y > $maxY) {
            $y = $maxY;
        }
        if ($qty !== '' || $batch !== '' || $ref !== '') {
            $this->text($img, $bold, $metaSize, $barcode, $pad, $y, $black, (int) ($w * 0.62));
            $metaX = (int) ($w * 0.64);
            if ($batch !== '') {
                $this->text($img, $bold, $metaSize, $batch, $metaX, $y, $black, $w - $pad - $metaX);
                $y += (int) ($metaSize + 3);
            }
            if ($ref !== '' && $y <= $maxY) {
                $this->text($img, $bold, $metaSize, $ref, $metaX, $y, $black, $w - $pad - $metaX);
            }
        } else {
            $this->centerText($img, $bold, $metaSize, $barcode, $w, $y, $black, $maxText);
        }

        return $img;
    }

    private function drawBarcode(\GdImage $canvas, string $src, int $x, int $y, int $w, int $h): void
    {
        $raw = $src;
        if (str_starts_with($src, 'data:')) {
            $parts = explode(',', $src, 2);
            $raw = base64_decode($parts[1] ?? '');
        }
        if ($raw === '' || $raw === false) {
            return;
        }

        $barcode = @imagecreatefromstring($raw);
        if (!$barcode) {
            return;
        }

        $srcW = imagesx($barcode);
        $srcH = imagesy($barcode);
        if ($srcW < 1 || $srcH < 1 || $w < 1 || $h < 1) {
            imagedestroy($barcode);

            return;
        }

        // Integer nearest-neighbor scale keeps bars crisp for scanners (no soft blur).
        $scaled = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($scaled, 255, 255, 255);
        $black = imagecolorallocate($scaled, 0, 0, 0);
        imagefill($scaled, 0, 0, $white);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, false);
        imagecopyresized($scaled, $barcode, 0, 0, 0, 0, $w, $h, $srcW, $srcH);

        // Threshold to pure B/W so gray anti-alias pixels do not merge bars.
        for ($py = 0; $py < $h; $py++) {
            for ($px = 0; $px < $w; $px++) {
                $rgb = imagecolorat($scaled, $px, $py);
                $luma = ((($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF)) / 3;
                imagesetpixel($scaled, $px, $py, $luma < 160 ? $black : $white);
            }
        }

        imagecopy($canvas, $scaled, $x, $y, 0, 0, $w, $h);
        imagedestroy($scaled);
        imagedestroy($barcode);
    }

    private function centerPriceLine(\GdImage $img, string $font, float $mrpSize, string $mrpPart, float $rateSize, string $ratePart, int $canvasW, int $y, int $color, int $maxWidth): void
    {
        if (!is_file($font)) {
            return;
        }

        $mrpBox = imagettfbbox($mrpSize, 0, $font, $mrpPart);
        $rateBox = imagettfbbox($rateSize, 0, $font, $ratePart);
        $mrpW = abs($mrpBox[2] - $mrpBox[0]);
        $rateW = abs($rateBox[2] - $rateBox[0]);
        $totalW = $mrpW + $rateW;
        if ($totalW > $maxWidth && $totalW > 0) {
            $scale = $maxWidth / $totalW;
            $mrpSize = max(6, $mrpSize * $scale);
            $rateSize = max(7, $rateSize * $scale);
            $mrpBox = imagettfbbox($mrpSize, 0, $font, $mrpPart);
            $rateBox = imagettfbbox($rateSize, 0, $font, $ratePart);
            $mrpW = abs($mrpBox[2] - $mrpBox[0]);
            $rateW = abs($rateBox[2] - $rateBox[0]);
            $totalW = $mrpW + $rateW;
        }

        $x = (int) (($canvasW - $totalW) / 2);
        imagettftext($img, $mrpSize, 0, $x, $y, $color, $font, $mrpPart);
        imagettftext($img, $rateSize, 0, $x + $mrpW, $y, $color, $font, $ratePart);
    }

    private function centerText(\GdImage $img, string $font, float $size, string $text, int $canvasW, int $y, int $color, int $maxWidth): void
    {
        $text = $this->fitText($font, $size, $text, $maxWidth);
        if ($text === '' || !is_file($font)) {
            return;
        }
        $box = imagettfbbox($size, 0, $font, $text);
        $textW = abs($box[2] - $box[0]);
        $x = (int) (($canvasW - $textW) / 2);
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    private function centerTextInBox(\GdImage $img, string $font, float $size, string $text, int $x1, int $x2, int $y, int $color): void
    {
        if ($text === '' || !is_file($font)) {
            return;
        }
        $box = imagettfbbox($size, 0, $font, $text);
        $textW = abs($box[2] - $box[0]);
        $x = $x1 + (int) ((($x2 - $x1) - $textW) / 2);
        imagettftext($img, $size, 0, max($x1, $x), $y, $color, $font, $text);
    }

    private function text(\GdImage $img, string $font, float $size, string $text, int $x, int $y, int $color, int $maxWidth): void
    {
        $text = $this->fitText($font, $size, $text, $maxWidth);
        if ($text === '' || !is_file($font)) {
            return;
        }
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    private function fitText(string $font, float $size, string $text, int $maxWidth): string
    {
        $text = trim($text);
        if ($text === '' || !is_file($font) || $maxWidth <= 0) {
            return $text;
        }

        while ($size > 6) {
            $box = imagettfbbox($size, 0, $font, $text);
            if (abs($box[2] - $box[0]) <= $maxWidth) {
                return $text;
            }
            $size -= 0.5;
        }

        while (strlen($text) > 1) {
            $text = substr($text, 0, -1);
            $box = imagettfbbox($size, 0, $font, $text . '...');
            if (abs($box[2] - $box[0]) <= $maxWidth) {
                return $text . '...';
            }
        }

        return $text;
    }

    private function fontHeight(string $font, float $size): int
    {
        if (!is_file($font)) {
            return (int) ceil($size + 4);
        }
        $box = imagettfbbox($size, 0, $font, 'Ay');
        return max(1, abs($box[7] - $box[1]));
    }

    private function fontAscent(string $font, float $size): int
    {
        if (!is_file($font)) {
            return (int) ceil($size);
        }
        $box = imagettfbbox($size, 0, $font, 'Ay');
        return max(1, abs($box[7]));
    }

    private function formatNumber($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        return \App\Models\Product::formatLabelAmount($value);
    }

    private function fontPath(bool $bold): string
    {
        $candidates = $bold
            ? [
                'C:/Windows/Fonts/arialbd.ttf',
                'C:/Windows/Fonts/Arialbd.ttf',
                'C:/Windows/Fonts/arialbd.TTF',
                'C:/Windows/Fonts/Arialbd.TTF',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
                base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf'),
                storage_path('fonts/DejaVuSans-Bold.ttf'),
                storage_path('fonts/nirmala_bold_d7dfffff841d352972725f34eeb9ace2.ttf'),
            ]
            : [
                'C:/Windows/Fonts/arial.ttf',
                'C:/Windows/Fonts/Arial.ttf',
                'C:/Windows/Fonts/arial.TTF',
                'C:/Windows/Fonts/Arial.TTF',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
                base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf'),
                storage_path('fonts/DejaVuSans.ttf'),
                storage_path('fonts/nirmala_normal_d97691210aa006fa64a256fd864749b8.ttf'),
            ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return $candidates[0];
    }
}

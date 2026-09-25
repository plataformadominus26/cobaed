    <?php
    // qrcode.php

    // Get parameters
    $data = isset($_GET['data']) ? $_GET['data'] : 'empty';
    $text = isset($_GET['text']) ? $_GET['text'] : 'QR Code';

    // Encode for API
    $encoded = urlencode("https://cobaed.robertoblancarte.com/newsboard.php?area=".$data);

    // QR API (goqr.me is free & stable)
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?data={$encoded}&size=400x400";

    // Fetch QR image from API
    $qrImage = imagecreatefrompng($qrUrl);
    if (!$qrImage) {
        die("Error: Unable to fetch QR code from API");
    }

    // Dimensions
    $qrWidth  = imagesx($qrImage);
    $qrHeight = imagesy($qrImage);

    // Custom font (optional: put a .ttf in same folder)
    $useTTF   = file_exists(__DIR__ . "/Roboto-Regular.ttf");
    $fontSize = 14; // for TTF
    $padding  = 40;

    if ($useTTF) {
        $bbox      = imagettfbbox($fontSize, 0, __DIR__ . "/Roboto-Regular.ttf", $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight= $bbox[1] - $bbox[7];
    } else {
        $font      = 5; // built-in font
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight= imagefontheight($font);
    }

    // Create final canvas
    $finalHeight = $qrHeight + $textHeight + $padding;
    $canvas      = imagecreatetruecolor($qrWidth, $finalHeight);

    // Colors
    $white = imagecolorallocate($canvas, 255, 255, 255);
    $black = imagecolorallocate($canvas, 0, 0, 0);
    imagefilledrectangle($canvas, 0, 0, $qrWidth, $finalHeight, $white);

    // Place QR
    imagecopy($canvas, $qrImage, 0, 0, 0, 0, $qrWidth, $qrHeight);

    // Place text centered
    $x = ($qrWidth - $textWidth) / 2;
    $y = $qrHeight + (($padding + $textHeight) / 2);

    if ($useTTF) {
        imagettftext($canvas, $fontSize, 0, $x, $y, $black, __DIR__ . "/Roboto-Regular.ttf", $text);
    } else {
        imagestring($canvas, $font, $x, $qrHeight + ($padding / 2), $text, $black);
    }

    // Output for download
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $text) . '.png"');
    imagepng($canvas);

    // Cleanup
    imagedestroy($qrImage);
    imagedestroy($canvas);
    ?>

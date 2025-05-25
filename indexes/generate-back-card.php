<?php
$accountNumber = $_GET['accountNumber'] ?? null;
$backgroundPath = '../assets/qr_codes/template.png';
$qrCodePath = "../assets/qr_codes/{$accountNumber}.png";
$outputPath = "../assets/access-card-back/{$accountNumber}-qr.png";

// Load background image
$background = imagecreatefrompng($backgroundPath);
if (!$background) {
    die("Failed to load background image.");
}

// Load QR code image
$qrCode = imagecreatefrompng($qrCodePath);
if (!$qrCode) {
    die("Failed to load QR code image.");
}

// Get dimensions
$bgWidth = imagesx($background);
$bgHeight = imagesy($background);
$qrWidth = imagesx($qrCode);
$qrHeight = imagesy($qrCode);

// Calculate position to center QR code
$x = (int) (($bgWidth - $qrWidth) / 2);
$y = (int) (($bgHeight - $qrHeight) / 2);

// Merge QR code onto background
imagecopy($background, $qrCode, $x, $y, 0, 0, $qrWidth, $qrHeight);

// Save result
imagepng($background, $outputPath);

// Free memory
imagedestroy($background);
imagedestroy($qrCode);

// Redirect
header("Location: ../manage-members.php?Success=Successfully created a new member!");
exit();

<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Create new TCPDF object for font conversion
$pdf = new TCPDF();

// Font variants to add
$fonts = [
    'Regular' => 'Nunito-Regular.ttf',
    'Bold' => 'Nunito-Bold.ttf',
    'Italic' => 'Nunito-Italic.ttf',
    'BoldItalic' => 'Nunito-BoldItalic.ttf',
    'Light' => 'Nunito-Light.ttf',
    'Medium' => 'Nunito-Medium.ttf',
    'SemiBold' => 'Nunito-SemiBold.ttf'
];

// Source and destination directories
$source_dir = __DIR__ . '/assets/fonts/receipt-fonts/';
$dest_dir = __DIR__ . '/vendor/tecnickcom/tcpdf/fonts/nunito/';

// Create destination directory if it doesn't exist
if (!file_exists($dest_dir)) {
    mkdir($dest_dir, 0777, true);
}

// Copy and add each font variant
foreach ($fonts as $type => $filename) {
    // Copy font file to TCPDF fonts directory
    $source = $source_dir . $filename;
    $dest = $dest_dir . $filename;
    
    if (file_exists($source)) {
        copy($source, $dest);
        // Add font to TCPDF
        $fontname = TCPDF_FONTS::addTTFfont($dest, 'TrueTypeUnicode', '', 96);
        echo "$type font added as: " . $fontname . "\n";
    } else {
        echo "Warning: Font file $filename not found in source directory\n";
    }
} 
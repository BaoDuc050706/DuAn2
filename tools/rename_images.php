<?php
// Script: tools/rename_images.php
// Purpose: Rename long image filenames in public/image to standardized short names.

$base = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
$map = [
    // existing filename => new standardized filename
    'Laptop ASUS ROG Strix G16 G615JHR-S5069W.jpg' => 'rog_strix_g16_g615jhr.jpg',
    'Laptop Asus ROG Strix Scar 16 G635 U93220G59  CPU Ultra 9-275HX  RAM 32GB DDR5  SSD 2TB PCIe  VGA RTX 5090 24GB  16.0 QHD 2K5 MiniLED IPS, 100% DCI-P3 & 240Hz  Win11.jpg' => 'rog_strix_scar16_g635.jpg',
    'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W.jpg' => 'asus_tuf_f16.jpg',
    'Laptop HP 15-FD0306TU A2NL7PA.jpg' => 'hp_15_fd0306tu.jpg',
    'Surface Laptop 7 15 inch Snapdragon X Elite32GB1TB(Chính hãng).jpg' => 'surface_laptop_7_15.jpg',
];

echo "Renaming images in: $base\n\n";

foreach ($map as $old => $new) {
    $oldPath = $base . $old;
    $newPath = $base . $new;

    if (!file_exists($oldPath)) {
        echo "SKIP: source not found: $old\n";
        continue;
    }

    if (file_exists($newPath)) {
        echo "SKIP: target already exists: $new\n";
        continue;
    }

    if (@rename($oldPath, $newPath)) {
        echo "RENAMED: '$old' -> '$new'\n";
    } else {
        echo "FAILED: could not rename '$old' -> '$new'\n";
    }
}

echo "\nDone.\n";

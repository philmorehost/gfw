<?php
// includes/image_handler.php

/**
 * Handle Image Upload with Compression
 */
function handle_image_upload($file, $destination_dir = 'assets/uploads/', $max_width = 1200, $quality = 75) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $upload_dir = __DIR__ . '/../' . $destination_dir;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_') . '.' . $extension;
    $target_file = $upload_dir . $filename;

    // Check if it's an image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return null;
    }

    // Process image
    switch ($extension) {
        case 'jpeg':
        case 'jpg':
            $image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'png':
            $image = imagecreatefrompng($file['tmp_name']);
            // Convert PNG to JPG for better compression if needed, but here we'll just compress
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'webp':
            $image = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            return null;
    }

    // Resize if necessary
    $width = imagesx($image);
    $height = imagesy($image);
    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
        $tmp_img = imagecreatetruecolor($new_width, $new_height);

        // Preserve transparency for PNG/WEBP
        if ($extension === 'png' || $extension === 'webp') {
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
        }

        imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);
        $image = $tmp_img;
    }

    // Save with compression
    $success = false;
    switch ($extension) {
        case 'jpeg':
        case 'jpg':
            $success = imagejpeg($image, $target_file, $quality);
            break;
        case 'png':
            // PNG quality is 0-9 (higher is more compression)
            $png_quality = floor((100 - $quality) / 10);
            $success = imagepng($image, $target_file, $png_quality);
            break;
        case 'webp':
            $success = imagewebp($image, $target_file, $quality);
            break;
    }

    imagedestroy($image);

    return $success ? '/' . $destination_dir . $filename : null;
}

<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

ini_set('memory_limit', '1024M');

use App\Models\Produk;
use App\Models\WarnaProduk;
use Illuminate\Support\Str;

// Configuration
$urlOverrides = [
    2 => 'https://www.laicaactive.com/products/laica-mesh-jacket-onyx',
    16 => 'https://www.laicaactive.com/products/laica-mens-tennis-polo-steel-blue',
    17 => 'https://www.laicaactive.com/products/laica-dynamic-mens-tee-onyx',
    18 => 'https://www.laicaactive.com/products/laica-mens-crop-jacket-forest',
    20 => 'https://www.laicaactive.com/products/laica-sports-cap-beton',
    23 => 'https://www.laicaactive.com/products/laica-mens-shorts-pants-forest',
];

// Gather all colors for suffix matching
$allColors = WarnaProduk::pluck('nama_warna')->toArray();

// Helper to fetch JSON from URL with proper User-Agent
function fetchUrl($url) {
    echo "Fetching: {$url}\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        return $res;
    }
    return null;
}

// Helper to parse base slug and color slug from URL
function getBaseAndColor($url, $allColors) {
    $path = parse_url($url, PHP_URL_PATH);
    $slug = basename($path);
    // Find matching color suffix
    foreach ($allColors as $c) {
        $cSlug = Str::slug($c);
        if (str_ends_with($slug, '-' . $cSlug)) {
            $baseSlug = substr($slug, 0, -strlen('-' . $cSlug));
            return [$baseSlug, $cSlug];
        }
    }
    return [$slug, null];
}

// Helper to save image and convert to webp if needed
function saveImage($imageUrl, $targetPath) {
    if (str_starts_with($imageUrl, '//')) {
        $imageUrl = 'https:' . $imageUrl;
    }
    
    // Remove query parameters
    $cleanUrl = strtok($imageUrl, '?');
    
    echo "  Downloading image: {$cleanUrl} -> " . basename($targetPath) . "\n";
    $imgData = fetchUrl($cleanUrl);
    if (!$imgData) {
        echo "    Error: Failed to download image from {$cleanUrl}\n";
        return false;
    }
    
    $dir = dirname($targetPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Convert to webp using GD
    $image = @imagecreatefromstring($imgData);
    if ($image !== false) {
        if (imageistruecolor($image) === false) {
            imagepalettetotruecolor($image);
        }
        if (imagewebp($image, $targetPath, 85)) {
            imagedestroy($image);
            return true;
        }
        imagedestroy($image);
    }
    
    // Fallback: raw save
    return file_put_contents($targetPath, $imgData) !== false;
}

$products = Produk::with(['images', 'details.warna', 'details.gambarDetail'])->get();

foreach ($products as $p) {
    echo "\n========================================\n";
    echo "Processing Product ID: {$p->produk_id} | Name: {$p->nama_produk}\n";
    
    // Skip product 1 if it only has absolute URLs in database
    if ($p->produk_id == 1) {
        echo "Skipping Product ID: 1 (LAICA TUMBHOLE JACKET) as it already uses absolute external CDN URLs.\n";
        continue;
    }
    
    // 1. Identify correct Shopify product page URL
    $descUrl = '';
    if (isset($urlOverrides[$p->produk_id])) {
        $descUrl = $urlOverrides[$p->produk_id];
    } else if (preg_match('/(https:\/\/www\.laicaactive\.com\/products\/[^\s\?]+)/', $p->deskripsi, $m)) {
        $descUrl = $m[1];
    } else {
        $descUrl = "https://www.laicaactive.com/products/" . $p->slug;
    }
    
    // Clean URL
    $descUrl = rtrim($descUrl, '.');
    if (str_ends_with($descUrl, 'the')) {
        $descUrl = substr($descUrl, 0, -3);
    }
    
    // 2. Fetch main product JSON
    $jsonContent = fetchUrl($descUrl . '.js');
    if (!$jsonContent) {
        echo "Error: Failed to fetch main product JSON from " . $descUrl . ".js\n";
        continue;
    }
    
    $mainData = json_decode($jsonContent, true);
    if (!$mainData || !isset($mainData['images'])) {
        echo "Error: Invalid JSON structure for product ID {$p->produk_id}\n";
        continue;
    }
    
    // 3. Process main product images (gambar_produk)
    $localImages = $p->images->filter(function($img) {
        return !str_starts_with($img->url_gambar, 'http');
    })->sortBy('urutan')->values();
    
    echo "Found " . count($localImages) . " local main images to restore.\n";
    foreach ($localImages as $index => $img) {
        $imageUrl = isset($mainData['images'][$index]) ? $mainData['images'][$index] : $mainData['images'][0];
        $targetPath = storage_path('app/public/' . ltrim($img->url_gambar, '/'));
        saveImage($imageUrl, $targetPath);
    }
    
    // 4. Process variant images (gambar_detail_produk)
    // Extract base slug and color slug of main URL
    list($baseSlug, $mainColorSlug) = getBaseAndColor($descUrl, $allColors);
    
    // Group variants by color
    $variantsByColor = [];
    foreach ($p->details as $d) {
        if ($d->warna) {
            $colorName = $d->warna->nama_warna;
            $variantsByColor[$colorName][] = $d;
        }
    }
    
    foreach ($variantsByColor as $colorName => $details) {
        $colorSlug = Str::slug($colorName);
        echo "Processing variants for color: {$colorName} (slug: {$colorSlug})\n";
        
        // Find all local image paths for these variants
        $localDetailImages = [];
        foreach ($details as $d) {
            foreach ($d->gambarDetail as $gd) {
                if (!str_starts_with($gd->url_gambar, 'http')) {
                    $localDetailImages[] = $gd;
                }
            }
        }
        
        if (empty($localDetailImages)) {
            echo "  No local detail images for color {$colorName}.\n";
            continue;
        }
        
        // Construct variant URL candidate
        $variantUrl = "https://www.laicaactive.com/products/" . $baseSlug . "-" . $colorSlug;
        $variantJsonContent = fetchUrl($variantUrl . '.js');
        
        $variantImageUrl = null;
        if ($variantJsonContent) {
            $variantData = json_decode($variantJsonContent, true);
            if ($variantData && isset($variantData['images'][0])) {
                $variantImageUrl = $variantData['images'][0];
            }
        }
        
        // Fallback: search main product JSON variants array
        if (!$variantImageUrl) {
            echo "  Variant URL failed or returned no images. Searching main product JSON variants...\n";
            if (isset($mainData['variants'])) {
                foreach ($mainData['variants'] as $v) {
                    if (isset($v['title']) && stripos($v['title'], $colorName) !== false) {
                        if (isset($v['featured_image']['src'])) {
                            $variantImageUrl = $v['featured_image']['src'];
                            echo "    Found matching variant image in main JSON: {$variantImageUrl}\n";
                            break;
                        }
                    }
                }
            }
        }
        
        // Final fallback: use main product first image
        if (!$variantImageUrl && isset($mainData['images'][0])) {
            $variantImageUrl = $mainData['images'][0];
            echo "    Fallback to main product first image.\n";
        }
        
        if ($variantImageUrl) {
            // Download once and copy/save to all target paths
            $tempPath = storage_path('app/public/temp_variant_' . $colorSlug . '.webp');
            if (saveImage($variantImageUrl, $tempPath)) {
                foreach ($localDetailImages as $gd) {
                    $targetPath = storage_path('app/public/' . ltrim($gd->url_gambar, '/'));
                    $dir = dirname($targetPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    copy($tempPath, $targetPath);
                    echo "    Copied variant image to {$gd->url_gambar}\n";
                }
                @unlink($tempPath);
            }
        } else {
            echo "  Error: Could not find any image URL for color variant {$colorName}.\n";
        }
    }
}

echo "\nDone updating all product and variant images!\n";

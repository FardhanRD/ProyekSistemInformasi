<?php

namespace App\Services;

use App\Models\MasterProduct;

class SkuGeneratorService
{
    public function generate(MasterProduct $masterProduct, string $size, string $color): string
    {
        $namePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $masterProduct->name), 0, 4));
        $sizePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $size), 0, 3));
        $colorPart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $color), 0, 3));

        $base = trim($namePart . '-' . $sizePart . '-' . $colorPart, '-');

        return $base . '-' . str_pad((string) $masterProduct->id, 4, '0', STR_PAD_LEFT);
    }
}

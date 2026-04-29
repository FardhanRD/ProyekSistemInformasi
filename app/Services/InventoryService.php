<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function increaseStock(ProductVariant $variant, int $qty, string $referenceType, ?int $referenceId, ?int $adminId, ?string $note = null): InventoryItem
    {
        return DB::transaction(function () use ($variant, $qty, $referenceType, $referenceId, $adminId, $note) {
            $inventory = InventoryItem::firstOrCreate(
                ['product_variant_id' => $variant->id],
                ['quantity' => 0, 'min_stock' => 5]
            );

            $before = (int) $inventory->quantity;
            $after = $before + $qty;

            $inventory->update([
                'quantity' => $after,
                'last_restock_at' => now(),
            ]);

            StockMovement::create([
                'inventory_item_id' => $inventory->id,
                'product_variant_id' => $variant->id,
                'movement_type' => 'in',
                'quantity' => $qty,
                'before_qty' => $before,
                'after_qty' => $after,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'created_by' => $adminId,
            ]);

            return $inventory;
        });
    }

    public function decreaseStock(ProductVariant $variant, int $qty, string $referenceType, ?int $referenceId, ?int $adminId, ?string $note = null): InventoryItem
    {
        return DB::transaction(function () use ($variant, $qty, $referenceType, $referenceId, $adminId, $note) {
            $inventory = InventoryItem::firstOrCreate(
                ['product_variant_id' => $variant->id],
                ['quantity' => 0, 'min_stock' => 5]
            );

            $before = (int) $inventory->quantity;
            $after = $before - $qty;

            if ($after < 0) {
                abort(422, 'Stok tidak mencukupi untuk varian SKU: ' . $variant->sku);
            }

            $inventory->update(['quantity' => $after]);

            StockMovement::create([
                'inventory_item_id' => $inventory->id,
                'product_variant_id' => $variant->id,
                'movement_type' => 'out',
                'quantity' => $qty,
                'before_qty' => $before,
                'after_qty' => $after,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'created_by' => $adminId,
            ]);

            return $inventory;
        });
    }

    public function adjustStock(ProductVariant $variant, int $newQty, ?int $adminId, ?string $note = null): InventoryItem
    {
        return DB::transaction(function () use ($variant, $newQty, $adminId, $note) {
            $inventory = InventoryItem::firstOrCreate(
                ['product_variant_id' => $variant->id],
                ['quantity' => 0, 'min_stock' => 5]
            );

            $before = (int) $inventory->quantity;
            $inventory->update(['quantity' => $newQty]);

            StockMovement::create([
                'inventory_item_id' => $inventory->id,
                'product_variant_id' => $variant->id,
                'movement_type' => 'adjustment',
                'quantity' => abs($newQty - $before),
                'before_qty' => $before,
                'after_qty' => $newQty,
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'note' => $note,
                'created_by' => $adminId,
            ]);

            return $inventory;
        });
    }
}

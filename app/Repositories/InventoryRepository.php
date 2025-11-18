<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;

class InventoryRepository
{
    public function findByProduct(int $productId, ?int $variantId = null): ?Inventory
    {
        $query = Inventory::where('product_id', $productId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        return $query->first();
    }

    public function createOrUpdate(array $data): Inventory
    {
        $inventory = Inventory::where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->first();

        if ($inventory) {
            $inventory->update($data);
            return $inventory->fresh();
        }

        return Inventory::create($data);
    }

    public function updateQuantity(Inventory $inventory, int $quantity, bool $reserve = false): bool
    {
        if ($reserve) {
            return $inventory->increment('reserved_quantity', $quantity);
        }

        return $inventory->increment('quantity', $quantity);
    }

    public function releaseReserved(Inventory $inventory, int $quantity): bool
    {
        return $inventory->decrement('reserved_quantity', $quantity);
    }

    public function deductQuantity(Inventory $inventory, int $quantity): bool
    {
        if ($inventory->reserved_quantity >= $quantity) {
            $inventory->decrement('reserved_quantity', $quantity);
        }

        return $inventory->decrement('quantity', $quantity);
    }

    public function getLowStockItems(int $threshold = null): Collection
    {
        $query = Inventory::with(['product', 'productVariant'])
            ->whereRaw('(quantity - reserved_quantity) <= low_stock_threshold');

        if ($threshold !== null) {
            $query->whereRaw('(quantity - reserved_quantity) <= ?', [$threshold]);
        }

        return $query->get();
    }
}


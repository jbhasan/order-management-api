<?php

namespace App\Services;

use App\Jobs\CheckLowStockJob;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;

class InventoryService
{
    public function __construct(
        private InventoryRepository $inventoryRepository
    ) {
    }

    public function updateInventory(int $productId, ?int $variantId, array $data): Inventory
    {
        $inventory = $this->inventoryRepository->createOrUpdate([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $data['quantity'] ?? 0,
            'reserved_quantity' => $data['reserved_quantity'] ?? 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 10,
            'location' => $data['location'] ?? null,
        ]);

        // Check for low stock
        CheckLowStockJob::dispatch($inventory);

        return $inventory;
    }

    public function getLowStockItems(int $threshold = null): \Illuminate\Database\Eloquent\Collection
    {
        return $this->inventoryRepository->getLowStockItems($threshold);
    }
}


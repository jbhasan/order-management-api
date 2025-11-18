<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // This would typically list inventory items
        // For now, return low stock items
        $items = $this->inventoryService->getLowStockItems();

        return response()->json($items);
    }

    /**
     * Update inventory.
     */
    public function update(Request $request, int $productId): JsonResponse
    {
        $request->validate([
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'reserved_quantity' => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'sometimes|integer|min:0',
            'location' => 'nullable|string',
        ]);

        try {
            $inventory = $this->inventoryService->updateInventory(
                $productId,
                $request->product_variant_id,
                $request->validated()
            );

            return response()->json($inventory);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get low stock items.
     */
    public function lowStock(Request $request): JsonResponse
    {
        $threshold = $request->get('threshold');
        $items = $this->inventoryService->getLowStockItems($threshold);

        return response()->json($items);
    }
}

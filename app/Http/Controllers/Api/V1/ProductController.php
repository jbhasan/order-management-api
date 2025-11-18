<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkImportProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['vendor_id', 'is_active', 'search', 'per_page']);

        // If vendor, only show their products
        if (auth()->user()->isVendor()) {
            $filters['vendor_id'] = auth()->id();
        }

        $products = $this->productService->getAll($filters);

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $vendorId = auth()->user()->isVendor() ? auth()->id() : null;
            $product = $this->productService->create($request->validated(), $vendorId);

            // Create variants if provided
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->create($variantData);

                    // Create inventory for variant
                    if (isset($variantData['quantity'])) {
                        (new InventoryRepository)->createOrUpdate([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'quantity' => $variantData['quantity'],
                            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 10,
                        ]);
                    }
                }
            }

            return response()->json($product->load(['vendor', 'variants', 'inventory']), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check vendor access
        if (auth()->user()->isVendor() && $product->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check vendor access
        if (auth()->user()->isVendor() && $product->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $product = $this->productService->update($product, $request->validated());
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check vendor access
        if (auth()->user()->isVendor() && $product->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $this->productService->delete($product);

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Search products.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json(['message' => 'Search query is required'], 422);
        }

        $products = $this->productService->search($query);

        return response()->json($products);
    }

    /**
     * Bulk import products from CSV.
     */
    public function bulkImport(BulkImportProductRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file);

            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($data[0] as $index => $row) {
                if ($index === 0) {
                    continue; // Skip header
                }

                try {
                    $productData = [
                        'name' => $row[0] ?? null,
                        'sku' => $row[1] ?? null,
                        'description' => $row[2] ?? null,
                        'price' => $row[3] ?? null,
                        'quantity' => $row[4] ?? 0,
                        'is_active' => isset($row[5]) ? (bool) $row[5] : true,
                    ];

                    if (!$productData['name'] || !$productData['sku'] || !$productData['price']) {
                        $errors[] = "Row {$index}: Missing required fields";
                        continue;
                    }

                    $vendorId = auth()->user()->isVendor() ? auth()->id() : null;
                    $this->productService->create($productData, $vendorId);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: {$e->getMessage()}";
                }
            }

            DB::commit();

            return response()->json([
                'message' => "Imported {$imported} products",
                'imported' => $imported,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

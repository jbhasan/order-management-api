<?php

namespace App\Services;

use App\Jobs\CheckLowStockJob;
use App\Models\Product;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class ProductService
{
	public function __construct(
		private ProductRepository $productRepository,
		private InventoryRepository $inventoryRepository
	) {}

	public function getAll(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
	{
		return $this->productRepository->all($filters);
	}

	public function getById(int $id): ?Product
	{
		return $this->productRepository->find($id);
	}

	public function create(array $data, ?int $vendorId = null): Product
	{
		if ($vendorId) {
			$data['vendor_id'] = $vendorId;
		}

		$product = $this->productRepository->create($data);

		// Create initial inventory record
		if (isset($data['quantity'])) {
			$inventory = $this->inventoryRepository->createOrUpdate([
				'product_id' => $product->id,
				'quantity' => $data['quantity'],
				'low_stock_threshold' => $data['low_stock_threshold'] ?? 10,
			]);

			// Check for low stock alert on creation
			CheckLowStockJob::dispatch($inventory);
		}

		return $product->load(['vendor', 'variants', 'inventory']);
	}

	public function update(Product $product, array $data): Product
	{
		$this->productRepository->update($product, $data);

		// Load variants to check if they exist
		$product->load('variants');

		// Update variant quantities if provided
		if (isset($data['variants']) && is_array($data['variants'])) {
			foreach ($data['variants'] as $variantData) {
				if (isset($variantData['id']) && isset($variantData['quantity'])) {
					$variant = $product->variants()->find($variantData['id']);

					if ($variant) {
						$inventory = $this->inventoryRepository->findByProduct(
							$product->id,
							$variant->id
						);

						if ($inventory) {
							$inventory = $this->inventoryRepository->createOrUpdate([
								'product_id' => $product->id,
								'product_variant_id' => $variant->id,
								'quantity' => $variantData['quantity'],
								'low_stock_threshold' => $variantData['low_stock_threshold'] ?? $inventory->low_stock_threshold,
							]);

							// Check for low stock alert on variant update
							CheckLowStockJob::dispatch($inventory);
						} else {
							// Create inventory if it doesn't exist
							$inventory = $this->inventoryRepository->createOrUpdate([
								'product_id' => $product->id,
								'product_variant_id' => $variant->id,
								'quantity' => $variantData['quantity'],
								'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 10,
							]);

							CheckLowStockJob::dispatch($inventory);
						}
					}
				}
			}
		}

		// Update base product quantity only if no variants exist
		// If variants exist, we typically manage inventory at variant level
		if (isset($data['quantity'])) {
			// If product has variants, only update base inventory if explicitly needed
			// Otherwise, update base product inventory
			$inventory = $this->inventoryRepository->findByProduct($product->id);

			if ($inventory) {
				$inventory = $this->inventoryRepository->createOrUpdate([
					'product_id' => $product->id,
					'quantity' => $data['quantity'],
					'low_stock_threshold' => $data['low_stock_threshold'] ?? $inventory->low_stock_threshold,
				]);

				// Check for low stock alert on update
				CheckLowStockJob::dispatch($inventory);
			} else {
				// Create inventory if it doesn't exist
				$inventory = $this->inventoryRepository->createOrUpdate([
					'product_id' => $product->id,
					'quantity' => $data['quantity'],
					'low_stock_threshold' => $data['low_stock_threshold'] ?? 10,
				]);

				CheckLowStockJob::dispatch($inventory);
			}
		}

		return $product->fresh(['vendor', 'variants', 'inventory']);
	}

	public function delete(Product $product): bool
	{
		return $this->productRepository->delete($product);
	}

	public function search(string $query): \Illuminate\Database\Eloquent\Collection
	{
		return $this->productRepository->search($query);
	}
}

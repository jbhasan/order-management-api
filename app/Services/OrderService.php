<?php

namespace App\Services;

use App\Jobs\CheckLowStockJob;
use App\Models\Order;
use App\Repositories\InventoryRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
	public function __construct(
		private OrderRepository $orderRepository,
		private InventoryRepository $inventoryRepository
	) {}

	public function create(array $data, int $customerId): Order
	{
		return DB::transaction(function () use ($data, $customerId) {
			$orderData = [
				'order_number' => Order::generateOrderNumber(),
				'customer_id' => $customerId,
				'status' => 'pending',
				'subtotal' => 0,
				'tax' => $data['tax'] ?? 0,
				'shipping_cost' => $data['shipping_cost'] ?? 0,
				'total' => 0,
				'shipping_address' => $data['shipping_address'],
				'billing_address' => $data['billing_address'],
				'payment_method' => $data['payment_method'] ?? null,
				'notes' => $data['notes'] ?? null,
			];

			$order = $this->orderRepository->create($orderData);
			$subtotal = 0;

			foreach ($data['items'] as $item) {
				$product = \App\Models\Product::findOrFail($item['product_id']);
				$variant = isset($item['product_variant_id'])
					? \App\Models\ProductVariant::find($item['product_variant_id'])
					: null;

				$unitPrice = $variant?->price ?? $product->price;
				$totalPrice = $unitPrice * $item['quantity'];
				$subtotal += $totalPrice;

				// Check inventory availability
				$inventory = $this->inventoryRepository->findByProduct(
					$product->id,
					$variant?->id
				);

				if (!$inventory || $inventory->available_quantity < $item['quantity']) {
					throw new \Exception("Insufficient inventory for product: {$product->name}");
				}

				// Reserve inventory
				$this->inventoryRepository->updateQuantity($inventory, $item['quantity'], true);

				// Create order item
				$order->items()->create([
					'product_id' => $product->id,
					'product_variant_id' => $variant?->id,
					'product_name' => $product->name,
					'product_sku' => $variant?->sku ?? $product->sku,
					'quantity' => $item['quantity'],
					'unit_price' => $unitPrice,
					'total_price' => $totalPrice,
				]);
			}

			$total = $subtotal + ($order->tax ?? 0) + ($order->shipping_cost ?? 0);
			$order->update([
				'subtotal' => $subtotal,
				'total' => $total,
			]);

			return $order->load(['customer', 'items.product', 'items.productVariant']);
		});
	}

	public function confirm(Order $order): Order
	{
		return DB::transaction(function () use ($order) {
			if ($order->status !== 'pending') {
				throw new \Exception('Order cannot be confirmed');
			}

			// Deduct inventory and check for low stock
			foreach ($order->items as $item) {
				$inventory = $this->inventoryRepository->findByProduct(
					$item->product_id,
					$item->product_variant_id
				);

				if ($inventory) {
					$this->inventoryRepository->deductQuantity($inventory, $item->quantity);

					// Refresh inventory to get updated quantity
					$inventory->refresh();

					// Check for low stock alert
					CheckLowStockJob::dispatch($inventory);
				}
			}

			$order->update([
				'status' => 'processing',
				'payment_status' => 'paid',
			]);

			return $order->fresh(['customer', 'items.product', 'items.productVariant']);
		});
	}

	public function cancel(Order $order): Order
	{
		return DB::transaction(function () use ($order) {
			if (!$order->canBeCancelled()) {
				throw new \Exception('Order cannot be cancelled');
			}

			// Restore inventory
			foreach ($order->items as $item) {
				$inventory = $this->inventoryRepository->findByProduct(
					$item->product_id,
					$item->product_variant_id
				);

				if ($inventory) {
					// Release reserved quantity
					$this->inventoryRepository->releaseReserved($inventory, $item->quantity);
					// Restore quantity if already deducted
					if ($order->status === 'processing') {
						$this->inventoryRepository->updateQuantity($inventory, $item->quantity);
					}
				}
			}

			$order->update([
				'status' => 'cancelled',
				'cancelled_at' => now(),
			]);

			return $order->fresh(['customer', 'items.product', 'items.productVariant']);
		});
	}

	public function updateStatus(Order $order, string $status): Order
	{
		$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
		if (!in_array($status, $validStatuses)) {
			throw new \Exception('Invalid status');
		}

		$updateData = ['status' => $status];

		if ($status === 'shipped') {
			$updateData['shipped_at'] = now();
		} elseif ($status === 'delivered') {
			$updateData['delivered_at'] = now();
		}

		$this->orderRepository->update($order, $updateData);

		return $order->fresh(['customer', 'items.product', 'items.productVariant']);
	}

	public function getAll(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
	{
		return $this->orderRepository->all($filters);
	}

	public function getById(int $id): ?Order
	{
		return $this->orderRepository->find($id);
	}

	public function getByCustomer(int $customerId, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
	{
		return $this->orderRepository->getByCustomer($customerId, $filters);
	}
}

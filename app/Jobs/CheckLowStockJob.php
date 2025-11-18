<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\LowStockAlert;
use App\Repositories\InventoryRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckLowStockJob implements ShouldQueue
{
	use Queueable;

	public function __construct(
		public Inventory $inventory
	) {}

	/**
	 * Execute the job.
	 */
	public function handle(InventoryRepository $inventoryRepository): void
	{
		// Refresh inventory to get latest quantity
		$this->inventory->refresh();

		if ($this->inventory->isLowStock()) {
			// Check if alert already exists and is not resolved
			$existingAlert = LowStockAlert::where('product_id', $this->inventory->product_id)
				->where('product_variant_id', $this->inventory->product_variant_id)
				->where('is_resolved', false)
				->first();

			if (!$existingAlert) {
				LowStockAlert::create([
					'product_id' => $this->inventory->product_id,
					'product_variant_id' => $this->inventory->product_variant_id,
					'current_quantity' => $this->inventory->available_quantity,
					'threshold' => $this->inventory->low_stock_threshold,
				]);

				// Send email alert
				SendLowStockAlertEmail::dispatch($this->inventory);
			}
		}
	}
}

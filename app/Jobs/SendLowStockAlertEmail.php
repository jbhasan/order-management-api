<?php

namespace App\Jobs;

use App\Models\Inventory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlertEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Inventory $inventory
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $inventory = $this->inventory->load(['product.vendor', 'productVariant']);

        $product = $inventory->product;
        $vendor = $product->vendor;

        if ($vendor) {
            Mail::send('emails.low-stock-alert', [
                'inventory' => $inventory,
                'product' => $product,
            ], function ($message) use ($vendor, $product) {
                $message->to($vendor->email, $vendor->name)
                    ->subject("Low Stock Alert: {$product->name}");
            });
        }

        // Also notify admin
        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            Mail::send('emails.low-stock-alert', [
                'inventory' => $inventory,
                'product' => $product,
            ], function ($message) use ($admin, $product) {
                $message->to($admin->email, $admin->name)
                    ->subject("Low Stock Alert: {$product->name}");
            });
        }
    }
}

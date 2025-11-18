<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdf implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order->load(['customer', 'items.product', 'items.productVariant']);

        $pdf = Pdf::loadView('invoices.order', ['order' => $order]);
        $filename = "invoices/{$order->order_number}.pdf";

        Storage::disk('public')->put($filename, $pdf->output());

        // Update order with invoice path if needed
        // $order->update(['invoice_path' => $filename]);
    }
}

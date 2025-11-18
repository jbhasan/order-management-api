<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\GenerateInvoicePdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $customer = $order->customer;

        // Generate invoice PDF in background
        GenerateInvoicePdf::dispatch($order);

        // Send confirmation email
        Mail::send('emails.order-confirmation', ['order' => $order], function ($message) use ($customer, $order) {
            $message->to($customer->email, $customer->name)
                ->subject("Order Confirmation: {$order->order_number}");
        });
    }
}

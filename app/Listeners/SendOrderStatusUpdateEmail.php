<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusUpdateEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $customer = $order->customer;

        Mail::send('emails.order-status-update', [
            'order' => $order,
            'oldStatus' => $event->oldStatus,
            'newStatus' => $event->newStatus,
        ], function ($message) use ($customer, $order) {
            $message->to($customer->email, $customer->name)
                ->subject("Order Status Update: {$order->order_number}");
        });
    }
}

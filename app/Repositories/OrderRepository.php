<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['customer', 'items.product', 'items.productVariant']);

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['order_number'])) {
            $query->where('order_number', $filters['order_number']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): ?Order
    {
        return Order::with(['customer', 'items.product', 'items.productVariant'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)
            ->with(['customer', 'items.product', 'items.productVariant'])
            ->first();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function getByCustomer(int $customerId, array $filters = []): LengthAwarePaginator
    {
        $query = Order::where('customer_id', $customerId)
            ->with(['items.product', 'items.productVariant']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}


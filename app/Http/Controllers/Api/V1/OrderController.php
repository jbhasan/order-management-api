<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'order_number', 'per_page']);

        // If customer, only show their orders
        if (auth()->user()->isCustomer()) {
            $orders = $this->orderService->getByCustomer(auth()->id(), $filters);
        } else {
            $orders = $this->orderService->getAll($filters);
        }

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->create($request->validated(), auth()->id());

            // Fire order created event
            event(new OrderCreated($order));

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check access
        if (auth()->user()->isCustomer() && $order->customer_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($order);
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        try {
            $oldStatus = $order->status;
            $order = $this->orderService->updateStatus($order, $request->status);

            // Fire status changed event
            if ($oldStatus !== $order->status) {
                event(new OrderStatusChanged($order, $oldStatus, $order->status));
            }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Confirm order (move from pending to processing).
     */
    public function confirm(int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        try {
            $oldStatus = $order->status;
            $order = $this->orderService->confirm($order);

            // Fire status changed event
            if ($oldStatus !== $order->status) {
                event(new OrderStatusChanged($order, $oldStatus, $order->status));
            }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check if customer owns the order or is admin/vendor
        if (auth()->user()->isCustomer() && $order->customer_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $oldStatus = $order->status;
            $order = $this->orderService->cancel($order);

            // Fire status changed event
            if ($oldStatus !== $order->status) {
                event(new OrderStatusChanged($order, $oldStatus, $order->status));
            }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

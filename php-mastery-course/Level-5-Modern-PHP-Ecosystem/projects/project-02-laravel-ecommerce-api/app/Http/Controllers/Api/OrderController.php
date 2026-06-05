<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()->orders()
            ->with(['items', 'payment'])
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        if ($order->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        return new OrderResource($order->load(['items', 'payment']));
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if (!$order->isCancellable()) {
            return response()->json(['message' => 'Order cannot be cancelled in its current state.'], 422);
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return response()->json([
            'message' => 'Order cancelled.',
            'order' => new OrderResource($order->fresh()->load('items')),
        ]);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $orders = Order::with(['user', 'items'])
            ->when($request->filled('status'), fn($q) => $q->byStatus($request->status))
            ->latest()
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', [
                Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED, Order::STATUS_DELIVERED, Order::STATUS_CANCELLED,
            ])],
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Order status updated.',
            'order' => new OrderResource($order->fresh()->load('items')),
        ]);
    }
}

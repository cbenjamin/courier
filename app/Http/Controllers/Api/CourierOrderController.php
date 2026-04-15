<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourierOrderController extends Controller
{
    public function __construct(private OrderStatusService $statusService) {}

    public function index(): AnonymousResourceCollection
    {
        $orders = Order::with('user', 'pickupLocation')
            ->whereIn('status', [Order::STATUS_CONFIRMED, Order::STATUS_PICKED_UP])
            ->orderBy('pickup_time')
            ->get();

        return OrderResource::collection($orders);
    }

    public function updateStatus(Request $request, Order $order): OrderResource|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::VALID_STATUSES)],
        ]);

        $order->load('subscription', 'user');
        $updated = $this->statusService->update($order, $validated['status']);

        return new OrderResource($updated->load('pickupLocation'));
    }

    public function updateLocation(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        // Broadcasting wired in Phase 2 — stub ready
        return response()->json(['ok' => true]);
    }
}

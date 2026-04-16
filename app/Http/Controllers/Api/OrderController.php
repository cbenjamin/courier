<?php

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
        $orders = $request->user()
            ->orders()
            ->with('pickupLocation')
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource|JsonResponse
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $order->load('pickupLocation', 'payment');

        return new OrderResource($order);
    }
}

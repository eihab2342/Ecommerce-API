<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Resources\Admin\Order\AdminOrderResource;
use Illuminate\Http\Request;
use App\Services\Admin\Order\OrderService;
use Illuminate\Routing\Controller as BaseController;

class AdminOrderController extends BaseController
{
    public function __construct(protected OrderService $orderService)
    {
        $this->middleware('auth:sanctum');
    }
    public function index()
    {
        $orders = $this->orderService->index();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'لا توجد طلبات معلقة'], 404);
        }

        return AdminOrderResource::collection($orders);
    }
    public function getPendingOrders()
    {
        $orders = $this->orderService->getPendingOrders();
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'لا توجد طلبات معلقة'], 404);
        }
        return AdminOrderResource::collection($orders);
    }
    public function getRevenue()
    {
        $revenue = $this->orderService->getRevenue();
        return response()->json(['revenue' => $revenue]);
    }
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,canceled',
        ]);

        $order = $this->orderService->updateOrderStatus($orderId, $request->status);
        if (!$order) {
            return response()->json(['message' => 'Order Not Found'], 404);
        }
        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'order' => new AdminOrderResource($order),
        ]);
    }
}
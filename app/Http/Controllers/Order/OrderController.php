<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Interfaces\Order\OrderInterface;
use App\Models\OrderItems;
use App\Models\products;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function __construct(protected OrderInterface $orderInterface) {}


    public function placeOrder(PlaceOrderRequest $request)
    {
        $orderData = $request->validated();
        $orderData['user_id'] = Auth::id();

        $order = $this->orderInterface->createOrder($orderData);
        return response()->json([
            'message' => 'تم تنفيذ الطلب بنجاح',
            'data' => new OrderResource($order)
        ]);
    }

    public function index()
    {
        $orders = Cache::remember('orders', 60 * 10, function () {
            return $this->orderInterface->getOrders();
        });

        return OrderResource::collection($orders);
    }
}
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PlaceOrderRequest;
use App\Http\Resources\User\OrderResource;
use App\Interfaces\Order\OrderInterface;
use App\Models\OrderItems;
use App\Models\products;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserOrderController extends Controller
{
    public function __construct(protected OrderInterface $orderInterface) {}


    public function placeOrder(array $orderData)
    {
        if (Auth::check()) {
            $orderData['user_id'] = Auth::id(); // أفضل وأسلم
            $orderData['ordered_at'] = $orderData['ordered_at'] ?? now();

            return $this->orderInterface->createOrder($orderData);
        }

        abort(401, 'Unauthorized');
    }

    public function index()
    {
        $orders = Cache::remember('orders', 60 * 10, function () {
            return $this->orderInterface->getOrders();
        });

        return OrderResource::collection($orders);
    }
}
<?php


namespace App\Repositories\Admin\Order;

use App\Interfaces\Admin\Order\AdminOrderInterface;
use App\Models\Order;
use Request;

class AdminOrderRepository implements AdminOrderInterface
{

    public function index()
    {
        return Order::with('items')->get();
    }

    public function getPendingOrders()
    {
        return Order::where('status', 'pending')->with('items')->get();
    }

    public function getRevenue(){
        return Order::where('status', 'delivered')->sum('total_price');
    }
    public function updateStatus(int $orderId, string $status)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return null;
        }

        $order->status = $status;
        $order->save();

        return $order;
    }
}
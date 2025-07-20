<?php

namespace App\Repositories\Order;

use App\Interfaces\Order\OrderInterface;
use App\Models\Order;

class OrderRepository implements OrderInterface
{
    public function getOrders()
    {
        return Order::with('items')->get();
    }

    public function createOrder(array $orderData): Order
    {
        $orderData['status'] = 'pending';
        $orderData['orderd_at'] = now();

        return Order::create($orderData);
    }}
<?php

namespace App\Services;

use App\Events\Order\OrderPlaced;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepo) {}

    public function createOrder(array $orderData): Order
    {
        $order = $this->orderRepo->createOrder($orderData);
        event(new OrderPlaced($order));
        return $order;
    }
}
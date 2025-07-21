<?php

namespace App\Interfaces\Order;

interface OrderInterface

{
    public function getOrders();
    public function createOrder(array $orderData);
}
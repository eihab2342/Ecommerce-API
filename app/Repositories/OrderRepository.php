<?php

// unused Repo ***********************
namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\OrderTest;

class OrderRepository
{
    public function getAllOrders()
    {
        return OrderTest::all();
    }
    public function getOrderById($orderId)
    {
        return OrderTest::findOrFail($orderId);
    }
    public function deleteOrder($orderId)
    {
        return OrderTest::destroy($orderId);
    }
    public function createOrder(array $orderDetails)
    {
        return OrderTest::create($orderDetails);
    }
    public function updateOrder($orderId, array $newDetails)
    {
        $order = OrderTest::find($orderId);

        if (!$order) {
            return null;
        }

        $order->update($newDetails);

        return $order;
    }
    public function getFulfilledOrders()
    {
        return OrderTest::where('is_fulfilled', true);
    }
}
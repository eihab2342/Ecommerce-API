<?php

namespace App\Services\Order;

use App\Events\Order\OrderPlaced;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\Expectation;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepo) {}

    public function getAllOrders()
    {
        return $this->orderRepo->getOrders();
    }
    public function createOrder(array $orderData): Order
    {
        try {
            if (empty($orderData['user_id'])) {
                throw new \Exception("Order Data is incomplete");
            }

            $orderData['ordered_at'] = $orderData['ordered_at'] ?? now();
            $order = $this->orderRepo->createOrder($orderData);

            event(new OrderPlaced($order));

            return $order;
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Product Not found");
        } catch (\Exception $e) {
            throw new \Exception("Something went wrong while place your order: " . $e->getMessage());
        }
    }
}
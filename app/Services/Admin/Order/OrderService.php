<?php

namespace App\Services\Admin\Order;

use App\Repositories\Admin\Order\AdminOrderRepository;

class OrderService
{
    public function __construct(protected AdminOrderRepository $orderRepo) {}
    public function index()
    {
        return $this->orderRepo->index();
    }

    public function getPendingOrders()
    {
        return $this->orderRepo->getPendingOrders();
    }

    public function getRevenue()
    {
        return $this->orderRepo->getRevenue();
    }
    public function updateOrderStatus(int $orderId, string $status)
    {
        return $this->orderRepo->updateStatus($orderId, $status);
    }
}
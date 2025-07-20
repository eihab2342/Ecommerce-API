<?php


namespace App\Interfaces\Admin\Order;

use Illuminate\Support\Facades\Request;

interface AdminOrderInterface
{
    public function index();
    public function getPendingOrders();
    public function getRevenue();
    public function updateStatus(int $orderId, string $status);
}
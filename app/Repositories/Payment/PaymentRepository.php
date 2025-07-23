<?php 

namespace App\Repositories\Payment;

use App\Interfaces\Payment\PaymobInteface;
use App\Models\Payments;

class PaymentRepository{
    public function storePayment(array $data)
    {
        return Payments::create($data);
    }
}
<?php 

namespace App\Interfaces\Payment;

use Faker\Provider\ar_EG\Payment;

interface PaymobInteface
{
    public function craetePayment(): Payment;
    public function verifyPayment(): bool;

}
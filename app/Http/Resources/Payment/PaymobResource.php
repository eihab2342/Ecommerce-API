<?php 

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymobResource extends JsonResource{
    public function toArray(Request $request):array{
        return [];
    }
} 
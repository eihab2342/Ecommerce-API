<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'user_id' => $this->user_id,
            'phone_number' => $this->phone_number,
            'total_price' => $this->total_price,
            'original_price' => $this->original_price,
            'status' => $this->status,
            'shipping_address' => $this->shipping_address,
            'village' => $this->village,
            'city' => $this->city,
            'governorate' => $this->governorate,
            'billing_address' => $this->billing_address,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'comments' => $this->comments,
            'ordered_at' => $this->ordered_at,
        ];
    }
}
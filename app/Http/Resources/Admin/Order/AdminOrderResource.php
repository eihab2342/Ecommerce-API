<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'client_name' => $this->name,
            'client_email' => $this->email,
            'client_phone' => $this->phone_number,
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'village' => $this->village,
            'city' => $this->city,
            'governorate' => $this->governorate,
            'payment_method' => $this->payment_method,
            'comments' => $this->comments,
            'total_price' => $this->total_price,
            'original_price' => $this->original_price,
            'status' => $this->status,
            'ordered_at' => $this->ordered_at,
            'items' => $this->items->map(function ($item) {
                return [
                    'order_id' => $item->order_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->total_price,
                    'image' => json_decode($item->image ?? '[]', true),
                ];
            }),
        ];
    }
}
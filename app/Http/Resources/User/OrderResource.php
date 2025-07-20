<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'client_name' => $this->name,
            'client_email' => $this->email,
            'client_phone' => $this->phone_number,
            'shipping_address' => $this?->shipping_address,
            'billing_address' => $this?->billing_address,
            'village' => $this?->village,
            'city' => $this?->city,
            'governorate' => $this?->governorate,
            'payment_method' => $this->order?->payment_method,
            'comments' => $this?->comments,
            'total_price' => $this?->total_price,
            'original_price' => $this?->original_price,
            'status' => $this?->status,
            'ordered_at' => $this?->ordered_at,
        ];
    }
}
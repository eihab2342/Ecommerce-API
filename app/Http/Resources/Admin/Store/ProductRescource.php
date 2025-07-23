<?php 

namespace App\Http\Resources\Admin\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRescource extends JsonResource{
    public function toArray(Request $request):array{
        $request = $request->validated();
        return [
            'name' => $request->name,
            // 
        ];
    }
}
<?php 

namespace App\Repositories\Store;

use App\Interfaces\Store\ProductInterface;
use App\Models\products;

class ProductRepository implements ProductInterface{
    public function create(array $data, ?array $images = null){
        return products::create($data);
    }

    public function delete($productId){
        $product = products::findOrFail($productId);
        return $product->delete();
    }
}
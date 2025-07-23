<?php 

namespace App\Repositories\Admin\Store;

use App\Interfaces\Store\ProductInterface;
use App\Models\products;

class ProductRepository implements ProductInterface{

    public function index(){
        return Products::with('images', 'subcategory')->get();
    }

    public function findOrfail($id){
        return Products::findOrFail($id);
    }
    public function create(array $data, ?array $images = null){
        return products::create($data);
    }

    public function update(array $data, $id){
        $product = $this->findOrfail($id);
        return $product->update($data);
    }
    public function delete($productId){
        $product = products::findOrFail($productId);
        return $product->delete();
    }
}
<?php

namespace App\Services\Store;

use App\Repositories\Admin\Store\ProductRepository;

class ProductService
{

    public function __construct(protected ProductRepository $productRepo) {}

    public function index(){
        return $this->productRepo->index();
    }
    
    public function findOrfail($id){
        return $this->productRepo->findOrfail($id);
    }
    public function create(array $data, ?array $images = null)
    {

        $product = $this->productRepo->create($data);

        if ($images) {
            foreach ($images as $key => $image) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName);

                $product->images()->create([
                    'images_path' => $imageName,
                    'is_main' => $key === 0 ? 1 : 0,
                ]);
            }
        }

        return $product;
    }

    public function update(array $data, $id){
        return $this->productRepo->update($data, $id);
    }
    public function delete($productId) {
        return $this->productRepo->delete($productId);
    }
}
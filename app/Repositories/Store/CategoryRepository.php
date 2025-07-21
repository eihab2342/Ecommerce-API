<?php

namespace App\Repositories\Store;

use App\Interfaces\Store\CategoryInterface;
use App\Models\categories;

class CategoryRepository
{

    public function __construct(protected CategoryInterface $categoryInterface) {}


    public function index()
    {
        return categories::select(['id', 'name', 'images'])
            ->with([
                'subcategories:id,category_id,name,image',
                'carouselImages:id,category_id,image_path,belongs_to,type'
            ])
            ->get();
    }
    public function findOrFail($id)
    {
        return categories::findOrFail($id);
    }

    public function store(array $data)
    {
        return categories::create($data);
    }

    public function update(array $data, categories $category)
    {
        return $category->update($data);
    }
}
<?php 

namespace App\Http\Controllers\User\Store;

use App\Models\categories;
use App\Models\products;

class ProductController{
    public function getproducts()
    {
        return response()->json(
            products::select('id', 'category_id', 'subcategory_id', 'name', 'description', 'price', 'old_price')
                ->with([
                    'category:id,name',
                    'subcategory:id,name',
                    'images:id,product_id,images_path,is_main'
                ])
                ->get()
        );
    }

    public function getAllCategoriesWithProducts()
    {
        $categories = categories::select('id', 'name')
            ->with([
                'subcategories:id,category_id,name',
                'carouselImages:id,subcategory_id,category_id,image_path,type',
                'subcategories.products:id,category_id,subcategory_id,name,description,price,old_price',
                'products:id,category_id,subcategory_id,name,description,price,old_price',
                'products.images:id,product_id,images_path,is_main'
            ])
            ->get();

        return response()->json($categories);
    }

    public function getproductById($id)
    {
        $product = products::select('id', 'category_id', 'subcategory_id', 'name', 'description', 'price', 'old_price', 'quantity',)
            ->with('category:id,name', 'subcategory:id,name', 'images:id,product_id,images_path')->find($id);

        if (!$product) {
            return response()->json(['message' => 'المنتج غير موجود'], 404);
        }

        return response()->json($product);
    }

    public function getProductByCategoryId($categoryId)
    {
        $category = Categories::with([
            'carouselImages:category_id,image_path,type,belongs_to',
            'products:id,category_id,name,description,price,old_price',
            'products.images:id,product_id,images_path',
            'subcategories:id,category_id,name,image',
            'subcategories.carouselImages:id,subcategory_id,image_path'
        ])
            ->select('id', 'name', 'description', 'images')
            ->findOrFail($categoryId);

        return response()->json([
            'products' => $category->products,
            'carousel_iamges' => $category->carouselImages,
            'subcategories' => $category->subcategories,
            'subcategoriesImages' => $category->subcategories->pluck('carouselImages')->flatten(),
        ]);
    }
}
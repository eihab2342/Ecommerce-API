<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\products;
use App\Http\Requests\Store\ProductRequest;
use App\Services\Store\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function __construct(protected ProductService $productService){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::with('images', 'subcategory')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();
        $images = $request->file('images');

        $this->productService->create($validatedData, $images);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة المنتج بنجاح',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProduct(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            // بقية الحقول
        ]);

        $product = Products::findOrFail($id); 
        $product->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث المنتج بنجاح',
            'data' => $product,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->productService->delete($id);
        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المنتج بنجاح',
        ]);
    }



    // ************************************
    // User methods

    public function getproducts()
    {
        return response()->json(
            Products::select('id', 'category_id', 'subcategory_id', 'name', 'description', 'price', 'old_price')
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
        $categories = Categories::select('id', 'name')
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
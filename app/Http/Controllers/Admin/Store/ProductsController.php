<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Store\ProductRescource;
use App\Models\categories;
use App\Models\products;
use App\Http\Requests\Store\ProductRequest;
use App\Services\Store\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function __construct(protected ProductService $productService){}
    
    public function index()
    {
        $products = $this->productService->index();
        return ProductRescource::collection($products);
    }

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
    public function updateProduct(ProductRequest $request, Products $product)
    {
        $data = $request->validated();
        $this->productService->update($data, $product->id);
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث المنتج بنجاح',
        ]);
    }
    
    public function destroy($id)
    {
        $this->productService->delete($id);
        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المنتج بنجاح',
        ]);
    }


}
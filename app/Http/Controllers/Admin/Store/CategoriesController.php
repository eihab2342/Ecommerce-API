<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;

use App\Http\Requests\Store\CategoryRequest;
use App\Models\carouselImages;
use App\Models\Categories;
use App\Models\Subcategory;
use App\Services\Store\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Str;

class CategoriesController extends Controller
{

    public function __construct(private CategoryService $categoryService) {}

    public function index()
    {
        $categories = $this->categoryService->index();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function getCategory($CategoryId)
    {
        $category = Categories::where('id', $CategoryId)
            ->select('id', 'name', 'description', 'images', 'created_at')
            ->with(['carouselImages:id,category_id,image_path', 'subcategories:id,category_id,name,description,image', 'subcategories.carouselImages:id,subcategory_id,image_path'])->get();
        return response()->json($category);
    }

    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $category = $this->categoryService->store($data);
            return response()->json([
                'message' => 'تم إنشاء الفئة بنجاح',
                'category' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في إنشاء الفئة: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeSubCategory(Request $request)
    {
        Log::info('Request data:', $request->all()); // لتسجيل القيم المستلمة

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subcategories,slug',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|mimes:jpeg,png,jpg,gif,svg,avif|max:2048',
            'carousel_images.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,avif|max:2048',
        ]);

        $subCategories = new Subcategory();
        $subCategories->name = $validatedData['name'];
        $subCategories->slug = $validatedData['slug'];
        $subCategories->description = $validatedData['description'] ?? null;
        $subCategories->category_id = $validatedData['category_id'];

        if ($request->hasFile('images')) {
            $image = $request->file('images'); // بدون [0]
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('subcategory', $fileName, 'public');
            $subCategories->image = $path; // تأكد من اسم العمود
        }

        $subCategories->save();

        // حفظ صور الكاروسيل
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $imageName = uniqid() . '_' . $carouselImage->getClientOriginalName();
                $path = $carouselImage->storeAs('carousel_images', $imageName, 'public');

                // إنشاء سجل جديد في جدول carousel_images
                $subCategories->carouselImages()->create([
                    'image_path' => $path,
                    'category_id' => $subCategories->id,
                    'belongs_to' => $subCategories->name,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الفئة الفرعية بنجاح',
            'data' => $subCategories,
        ]);
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            $this->categoryService->update($request->validated(), $request, $id);
            return response()->json(['message' => 'تم تحديث الفئة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل في التحديث: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->destroy($id);
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل في الحذف: ' . $e->getMessage()], 500);
        }
    }




    /************************************************************************************* */
    public function getSubCategories($CategoryId)
    {
        $subcategories = SubCategory::where('Category_id', $CategoryId)->select('id', 'category_id', 'name')->get();
        return response()->json($subcategories);
    }
    public function destroySubCategory($id)
    {
        $subCategory = Subcategory::find($id);

        if (!$subCategory) {
            return response()->json(['message' => 'Sub Category not found'], 404);
        }

        // حذف الصورة من public/categories لو موجودة
        if ($subCategory->image_path && file_exists(public_path($subCategory->image_path))) {
            unlink(public_path($subCategory->image_path));
        }

        // حذف الساب كاتيجوري
        $subCategory->delete();

        return response()->json(['message' => 'Sub Category deleted successfully'], 200);
    }
    public function deleteCarouselImage($id)
    {
        $image = carouselImages::find($id);

        if (!$image) {
            return response()->json(['message' => 'الصورة غير موجودة'], 404);
        }

        try {
            // حذف من storage
            Storage::delete($image->image_path);

            if (strpos($image->image_path, 'carousel_images') !== false) {
                $publicPath = public_path($image->image_path);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }
            }

            $image->delete();

            return response()->json(['message' => 'تم حذف الصورة بنجاح'], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to delete image', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'حدث خطأ أثناء حذف الصورة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
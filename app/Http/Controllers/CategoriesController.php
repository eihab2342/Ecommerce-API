<?php

namespace App\Http\Controllers;

use App\Models\carouselImages;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Str;

class CategoriesController extends Controller
{
    // -----------------------------
    // Admin methods
    // -----------------------------


    public function index()
    {
        $categories = categories::select(['id', 'name', 'images'])
            ->with([
                'subcategories:id,category_id,name,image',
                'carouselImages:id,category_id,image_path,belongs_to,type'
            ])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    // getCategory
    public function getCategory($CategoryId)
    {
        $category = Categories::where('id', $CategoryId)
            ->select('id', 'name', 'description', 'images', 'created_at')
            ->with(['carouselImages:id,category_id,image_path', 'subcategories:id,category_id,name,description,image', 'subcategories.carouselImages:id,subcategory_id,image_path'])->get();
        return response()->json($category);
    }

    /**
     * Get subcategories for a specific Categories.
     */
    public function getSubCategories($CategoryId)
    {
        $subcategories = SubCategory::where('Category_id', $CategoryId)->select('id', 'category_id', 'name')->get();
        return response()->json($subcategories);
    }

    /**
     * Store a newly created Categories.
     */
    public function store(Request $request)
    {
        // التحقق من المدخلات
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'images' => 'nullable|mimes:jpeg,png,jpg,gif,svg,avif|max:2048',
            'carousel_images.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,avif|max:2048', // تغيير هنا
        ]);

        Log::info('Request Data:', [
            'name' => $request->name,
            'description' => $request->description,
            'has_images' => $request->hasFile('images'),
            'has_carousel' => $request->hasFile('carousel_images'),
            'carousel_count' => $request->hasFile('carousel_images') ? count($request->file('carousel_images')) : 0,
        ]);

        // إنشاء slug إذا لم يتم تقديمه
        $slug = $request->slug ?? Str::slug($request->name);
        // إنشاء فئة جديدة
        $category = new Categories();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->slug = $request->slug ?? Str::slug($request->name);

        // حفظ الصورة الرئيسية
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('categories', $fileName, 'public');
            $category->images = $path;
        }

        // حفظ الفئة في قاعدة البيانات
        $category->save();

        // حفظ صور الكاروسيل
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $imageName = uniqid() . '_' . $carouselImage->getClientOriginalName();
                $path = $carouselImage->storeAs('carousel_images', $imageName, 'public');

                // إنشاء سجل جديد في جدول carousel_images
                $category->carouselImages()->create([
                    'image_path' => $path,
                    'category_id' => $category->id, // ربط الصورة بالفئة
                    'belongs_to' => $category->name,
                ]);
            }
        }

        return response()->json([
            'message' => 'تم إنشاء الفئة بنجاح',
            'category' => $category,
        ], 201);
    }

    /**
     * Store a newly created subCategories.
     */
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

    /**
     * Update the specified Categories.
     */
    public function update(Request $request, $id)
    {
        $Categories = Categories::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'images' => 'nullable|array',
            'carousel_images' => 'nullable|array',
        ]);

        $Categories->name = $request->input('name');
        $Categories->description = $request->input('description');
        $Categories->slug = $request->input('slug') ?? Str::slug($request->name);

        if ($request->hasFile('images')) {
            // حذف الصورة القديمة لو عايز تنظف
            if ($Categories->images && Storage::disk('public')->exists($Categories->images)) {
                Storage::disk('public')->delete($Categories->images);
            }

            $image = $request->file('images')[0];
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('categories', $fileName, 'public');
            $Categories->images = $path;
        }

        $Categories->save();

        if ($request->hasFile('carousel_images')) {
            // ممكن تضيف هنا حذف الصور القديمة لو عايز
            foreach ($request->file('carousel_images') as $carouselImage) {
                $uniqueName = uniqid() . '_' . $carouselImage->getClientOriginalName();
                $path = $carouselImage->storeAs('category_carousel_images', $uniqueName, 'public');
                $Categories->carouselImages()->create([
                    'image_path' => $path,
                    'Categories_id' => $Categories->id,
                    'belongs_to' => $Categories->name
                ]);
            }
        }

        return response()->json(['message' => 'تم تحديث الفئة بنجاح']);
    }

    /**
     * Delete the specified category along with its images and related carousel images.
     */
    public function destroy($id)
    {
        $category = categories::findOrFail($id);
        // حذف صورة الكاتيجوري الرئيسية لو موجودة
        if ($category->images && file_exists(public_path($category->images))) {
            unlink(public_path($category->images));
        }

        // حذف صور الكاروسيل المرتبطة بالكاتيجوري
        foreach ($category->carouselImages as $carouselImage) {
            if ($carouselImage->image_path && file_exists(public_path($carouselImage->image_path))) {
                unlink(public_path($carouselImage->image_path));
            }
            $carouselImage->delete();
        }

        // حذف الساب كاتيجوريز المرتبطة وصورهم
        foreach ($category->subCategories as $subCategory) {
            if ($subCategory->image_path && file_exists(public_path($subCategory->image_path))) {
                unlink(public_path($subCategory->image_path));
            }
            $subCategory->delete();
        }

        $category->delete();

        return response()->json(['message' => ' Category deleted successfully'], 200);
    }

    /**
     * Delete the specified SubCategory along with its images.
     */
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


    // CarouselImageController.php
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
    }    // -----------------------------
    // User API methods
    // -----------------------------

    /**
     * Get all categories with subcategories & carousel images.
     */
    public function categories()
    {
        $categories = Categories::select(['id', 'name', 'images'])
            ->with([
                'subcategories',
                'carouselImages'
            ])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Show a specific Categories with its subcategories.
     */
    public function showCategories($id)
    {
        $Categories = Categories::with('subcategories')->find($id);

        if (!$Categories) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $Categories
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\carouselImages;
use App\Models\images;
use Illuminate\Http\Request;

class CarouselImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // افترض أن لديك جدول `carousel_images` أو ما شابه
        $images = carouselImages::whereNull('belongs_to')->get();  // جلب جميع الصور
        return response()->json(['image_paths' => $images], 200);
    }

    /**
     * Store multiple images.
     */

    public function store(Request $request)
    {
        // 🔹 تحقق من صحة البيانات
        $request->validate([
            'images' => 'required|array', 
            // 'images.*' => 'mimes:jpeg,png,jpg,gif,svg,avif', // تحقق من نوع وحجم كل صورة
            'type' => 'required|in:adv,banner,carousel', // تحقق من أن النوع ينتمي إلى القيم المسموح بها
            'belongsTo' => 'nullable|string|max:255',
        ]);

        // 🔹 تحديد المجلد الذي سيتم تخزين الصور فيه
        $imageFolder = 'carousel_images';  // هذا المجلد يجب أن يكون داخل `storage/app/public`

        // 🔹 حفظ الصور في المجلد `storage/app/public/carousel_images`
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            // استخدام اسم الصورة الأصلي فقط
            $imageName = $image->getClientOriginalName();

            $path = $image->storeAs("$imageFolder", $imageName);

            $imagePaths[] = $imageName;  // حفظ اسم الصورة فقط في قاعدة البيانات
        }

        foreach ($imagePaths as $imageName) {
            carouselImages::create([
                'image_path' => $imageName,  // حفظ اسم الصورة فقط في قاعدة البيانات
                'type' => $request->type,
                'belongsTo' => $request->belongsTo,
            ]);
        }

        return response()->json(['message' => 'تم رفع الصور بنجاح!', 'image_paths' => $imagePaths], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // العثور على الصورة من خلال الـ ID
        $image = carouselImages::find($id);

        if (!$image) {
            return response()->json(['message' => 'الصورة غير موجودة'], 404);
        }

        // حذف الصورة من المجلد
        $imagePath = public_path('storage/carousel_images/' . $image->image_path);
        if (file_exists($imagePath)) {
            unlink($imagePath); // حذف الملف من السيرفر
        }

        // حذف السجل من قاعدة البيانات
        $image->delete();

        return response()->json(['message' => 'تم حذف الصورة بنجاح'], 200);
    }

    // *********************************************************************
    //User API Methods
    public function getCarouselImages()
    {
        $carousel_images = carouselImages::where('type', 'carousel')->whereNull('belongs_to')->get();
        $adv_images = carouselImages::where('type', 'adv')->get();
        return response()->json([
            'carousel_images' => $carousel_images,
            'adv_images' => $adv_images,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;

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
        $images = carouselImages::whereNull('belongs_to')->get();
        return response()->json(['image_paths' => $images], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'type' => 'required|in:adv,banner,carousel',
            'belongsTo' => 'nullable|string|max:255',
        ]);

        $imageFolder = 'carousel_images';

        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $imageName = $image->getClientOriginalName();

            $path = $image->storeAs("$imageFolder", $imageName);

            $imagePaths[] = $imageName; 
        }

        foreach ($imagePaths as $imageName) {
            carouselImages::create([
                'image_path' => $imageName,
                'type' => $request->type,
                'belongsTo' => $request->belongsTo,
            ]);
        }

        return response()->json(['message' => 'تم رفع الصور بنجاح!', 'image_paths' => $imagePaths], 200);
    }

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
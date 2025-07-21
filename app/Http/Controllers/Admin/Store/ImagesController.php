<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ImageRequest;
use App\Models\carouselImages;
use App\Services\Store\ImageService;
use Storage;

class ImagesController extends Controller
{

    public function __construct(private ImageService $imageService) {}
    public function index()
    {
        $images = carouselImages::orWhereNull('belongs_to')->get();
        return view('admin.settings.add-image', compact('images'));
    }

    public function store(ImageRequest $request)
    {
        try {
            $this->imageService->store($request);
            return redirect()->back()->with('success', 'تم رفع الصورة بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل في رفع الصورة: ' . $e->getMessage());
        }
    }

    public function deleteImage($id)
    {
        $image = carouselImages::findOrFail($id); // البحث عن الصورة

        // حذف الصورة من التخزين
        $imagePath = storage_path('app/public/' . $image->image);

        if (file_exists($imagePath) && is_file($imagePath)) {
            Storage::disk('public')->delete($image->image);
        } else {
            // dd("File not found or not a file.");
        }

        // حذف الصورة من قاعدة البيانات
        $image->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح!']);
    }
}
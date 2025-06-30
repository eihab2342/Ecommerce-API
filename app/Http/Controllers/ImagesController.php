<?php

namespace App\Http\Controllers;

use App\Models\carouselImages;
use App\Models\images;
use Illuminate\Http\Request;
use Storage;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $images = carouselImages::orWhereNull('belongs_to')->get();
        return view('admin.settings.add-image', compact('images'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 🔹 تحقق من صحة البيانات
        $request->validate([
            'image_path' => 'required',
            'type' => 'required|string',
            'belongsTo' => 'nullable|string|max:255',
        ]);

        // 🔹 حفظ الصورة في مجلد `storage/app/public/images`
        $path = $request->file('image')->store('uploads', 'public');

        // 🔹 تخزين معلومات الصورة في قاعدة البيانات
        images::create([
            'image_path' => $path,
            'type' => $request->location,
            'belongsTo' => $request->description,
        ]);

        // 🔹 إعادة توجيه مع رسالة نجاح
        return redirect()->back()->with('success', 'تم رفع الصورة بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(images $images)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(images $images)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, images $images)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
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
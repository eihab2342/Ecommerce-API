<?php

namespace App\Services\Store;

use App\Repositories\Store\ImageRepository;
use Illuminate\Http\Request;

class ImageService
{

    public function __construct(protected ImageRepository $imageRepo) {}
    public function store(Request $request)
    {
        $data = $request->validated();

        // حفظ الصورة
        if ($request->hasFile('image')) {
            $data['path'] = $request->file('image')->store('uploads', 'public');
        } else {
            throw new \Exception('الصورة غير موجودة');
        }

        // حفظ بيانات الصورة في قاعدة البيانات
        $this->imageRepo->store($data);
    }
}
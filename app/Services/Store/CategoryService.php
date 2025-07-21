<?php 

namespace App\Services\Store;

use App\Http\Requests\Store\CategoryRequest;
use App\Models\categories;
use App\Repositories\Store\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryService{

    public function __construct(protected CategoryRepository $categoryRepo){}
    public function index(){
        return $this->categoryRepo->index();
    }
    public function store(CategoryRequest $request): Categories
    {
        $data = $request->validated();
        $category = new Categories();
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->slug = $data['slug'] ?? Str::slug($data['name']);

        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('categories', $fileName, 'public');
            $category->images = $path;
        }

        $category->save();

        // حفظ صور الكاروسيل
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $imageName = uniqid() . '_' . $carouselImage->getClientOriginalName();
                $path = $carouselImage->storeAs('carousel_images', $imageName, 'public');

                $category->carouselImages()->create([
                    'image_path' => $path,
                    'category_id' => $category->id,
                    'belongs_to' => $category->name,
                ]);
            }
        }

        return $category;
    }

    public function update(array $data, Request $request, $id)
    {
        $category = $this->categoryRepo->findOrFail($id);

        $category->name = $data['name'];
        $category->description = $data['description'];
        $category->slug = $data['slug'] ?? \Str::slug($data['name']);

        if ($request->hasFile('images')) {
            // حذف القديمة
            if ($category->images && \Storage::disk('public')->exists($category->images)) {
                \Storage::disk('public')->delete($category->images);
            }

            $image = $request->file('images')[0];
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('categories', $fileName, 'public');
            $category->images = $path;
        }

        $category->save();

        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $uniqueName = uniqid() . '_' . $carouselImage->getClientOriginalName();
                $path = $carouselImage->storeAs('category_carousel_images', $uniqueName, 'public');

                $category->carouselImages()->create([
                    'image_path' => $path,
                    'Categories_id' => $category->id,
                    'belongs_to' => $category->name,
                ]);
            }
        }

        return $category;
    }

    public function destroy($id)
    {
        $category = $this->categoryRepo->findOrFail($id);

        if ($category->images && \Storage::disk('public')->exists($category->images)) {
            \Storage::disk('public')->delete($category->images);
        }

        foreach ($category->carouselImages as $carouselImage) {
            if ($carouselImage->image_path && \Storage::disk('public')->exists($carouselImage->image_path)) {
                \Storage::disk('public')->delete($carouselImage->image_path);
            }
            $carouselImage->delete();
        }

        foreach ($category->subCategories as $subCategory) {
            if ($subCategory->image_path && \Storage::disk('public')->exists($subCategory->image_path)) {
                \Storage::disk('public')->delete($subCategory->image_path);
            }
            $subCategory->delete();
        }

        $category->delete();
    }
}
<?php


/**
 * Well be update soon
 */
namespace App\Http\Controllers\User\Store;

use App\Http\Controllers\Controller;
use App\Models\categories;

class CategoryController extends Controller
{

    public function categories()
    {
        $categories = categories::select(['id', 'name', 'images'])
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
<?php

namespace Database\Seeders;

use App\Models\categories;
use App\Models\Category; // تأكد من استيراد النموذج الصحيح
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // إنشاء فئات رئيسية
        $categories = [
            [
                'name' => 'إلكترونيات',
                'slug' => 'electronics',
                'images' => 'noon-category-electronics.avif'
            ],
            [
                'name' => 'ملابس',
                'slug' => 'clothing',
                'images' => 'clothes.avif'
            ],
            [
                'name' => 'أثاث',
                'slug' => 'furniture',
                'images' => 'furniture.avif'
            ],
        ];

        foreach ($categories as $category) {
            $createdCategory = categories::create($category); // استخدام النموذج بحالة الأحرف الصحيحة

            // إنشاء فئات فرعية لكل فئة رئيسية
            $subcategories = [
                [
                    'name' => $createdCategory->name == 'إلكترونيات' ? 'هواتف' : ($createdCategory->name == 'ملابس' ? 'رجالي' : 'غرف نوم'),
                    'slug' => $createdCategory->slug . '-sub1',
                    'image' => $createdCategory->images,// يمكنك تغيير هذا إذا كنت تريد صورًا مختلفة
                    'category_id' => $createdCategory->id // الاسم الصحيح للمفتاح الخارجي
                ],
                [
                    'name' => $createdCategory->name == 'إلكترونيات' ? 'حواسيب' : ($createdCategory->name == 'ملابس' ? 'نسائي' : 'صالات'),
                    'slug' => $createdCategory->slug . '-sub2',
                    'image' => $createdCategory->images,
                    'category_id' => $createdCategory->id // الاسم الصحيح للمفتاح الخارجي
                ],
            ];

            foreach ($subcategories as $subcategory) {
                $createdCategory->subcategories()->create($subcategory);
            }
        }
    }
}
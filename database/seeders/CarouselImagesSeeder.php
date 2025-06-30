<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarouselImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('carousel_images')->insert([
            [
                'image_path' => 'noon-adv.avif',
                'belongs_to' => '',
                'type' => 'adv',
            ],
        ]);
    }
}
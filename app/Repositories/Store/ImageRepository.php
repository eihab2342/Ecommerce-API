<?php

namespace App\Repositories\Store;

use App\Models\Images;

class ImageRepository
{
    public function store(array $data)
    {
        return Images::create($data);
    }
}
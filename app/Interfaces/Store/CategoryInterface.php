<?php 

namespace App\Interfaces\Store;

interface CategoryInterface{
    public function index();
    public function store(array $data);
    public function update($id, array $data);
}
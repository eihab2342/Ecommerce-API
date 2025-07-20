<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTest extends Model
{
    /** @use HasFactory<\Database\Factories\OrderTestFactory> */
    use HasFactory;


    protected $fillable = ['client', 'details', 'is_fulfilled'];
}
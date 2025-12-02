<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'image',
        'address_first_line',
        'address_second_line',
        'address_third_line'
    ];
      
}

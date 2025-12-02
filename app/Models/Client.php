<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'brand_name'];
    
    public function get_total_amount(){
        return $this->hasMany(Payment::class, 'client_id', 'id')->where('status', 2)->where('show_status', 0)->sum('price');
    }

    public function brand(){
        return $this->hasOne(Brands::class, 'id', 'brand_name');
    }
}

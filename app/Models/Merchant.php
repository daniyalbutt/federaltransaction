<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    public function getMerchant(){
        if($this->merchant == 0){
            return 'STRIPE';
        }else if($this->merchant == 3){
            return 'FETCH';
        }else if($this->merchant == 4){
            return 'AUTHORIZE';
        }else if($this->merchant == 5){
            return 'PAYPAL';
        }
    }
}

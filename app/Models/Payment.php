<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function get_status(){
        $status = $this->status;
        if($status == 0){
            return 'PENDING';
        }elseif($status == 1){
            return 'DECLINED';
        }elseif($status == 2){
            return 'SUCCESS';
        }
    }

    public function get_badge_status(){
        $status = $this->status;
        if($status == 0){
            return 'btn-warning';
        }elseif($status == 1){
            return 'btn-danger';
        }elseif($status == 2){
            return 'btn-success';
        }
    }
    public function get_badge_invoice_status(){
        $status = $this->status;
        if($status == 0){
            return 'badge text-bg-warning text-white';
        }elseif($status == 1){
            return 'badge text-bg-danger text-white';
        }elseif($status == 2){
            return 'badge text-bg-success text-white';
        }
    }
    
    public function getCard(){
        if($this->merchants != null){
            $merchant = $this->merchants->merchant;
            if($merchant == 0){
                return strtoupper(json_decode($this->return_response)->payment_method_details->card->brand) . ' **** **** ****' . json_decode($this->return_response)->payment_method_details->card->last4;
            }else if($merchant == 4){
                // Authorize
                $data = json_decode($this->payment_data);
                if ($data && isset($data->cc_number)) {
                    return ' **** **** **** ' . substr($data->cc_number, -4);
                }
                return '';
            }else if($merchant == 3){
                return ' **** **** **** ' .substr(json_decode($this->payment_data)->cardnumber, -4);
            }
        }else{
            return '';
        }
    }
    
    public function getCardBrand(){
        if($this->merchants != null){
            $merchant = $this->merchants->merchant;
            if($merchant == 0){
                return strtoupper(json_decode($this->return_response)->payment_method_details->card->brand);
            }else if($merchant == 4){
                $response = json_decode($this->authorize_response);
                if ($response && isset($response->card_brand)) {
                    return strtoupper($response->card_brand);
                }
                return ''; 
            }else if($merchant == 3){
                return 'FETCH';
            }
        }else{
            return '';
        }
    }
    
    public function getMerchant(){
        if($this->merchant == 0){
            return 'STRIPE';
        }else if($this->merchant == 3){
            return 'FETCH';
        }else if($this->merchant == 4){
            return 'AUTHORIZE';
        }
    }

    public function merchants(){
        return $this->hasOne(Merchant::class, 'id', 'merchant');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

}

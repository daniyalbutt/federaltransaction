<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'name',
        'description',
        'fee_amount',
        'fee_code',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

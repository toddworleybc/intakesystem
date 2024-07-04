<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{
    use HasFactory;


    protected $fillable = [
        'payment_method',
        'amount',
        'for',
        'initial_payment',
        'payment_sent_count',
        'status',
        'invoice_id'
    ];



    public function client(): BelongsTo {
        return $this->belongsTo(Clients::class, 'clients_id');
    }




}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    use HasFactory;


    protected $fillable = [
        'payment_method',
        'amount',
        'for',
        'payment_sent_count',
        'status',
        'invoice_id',
        'frequency',
        'receipt_sent_dates',
        'card_amount',
        'processing_fee',
        'notes',
        'payment_link',
        'payment_welcome_email'
    ];

     // Casts ===============/

     protected function casts(): array
     {
         return [
             'payment_sent_count' => 'array',
             'receipt_sent_dates' => 'array'
         ];
     }


    public function client(): BelongsTo {
        return $this->belongsTo(Clients::class, 'clients_id');
    }

    
// Accessors an Mutators ===/

    protected function paymentSentCount(): Attribute
    {
        return Attribute::make(
            set: fn (array $value) => json_encode($value),
        );
    }


    protected function receiptSentDates(): Attribute
    {
        return Attribute::make(
            set: fn (array $value) => json_encode($value),
        );
    }

    protected function paymentWelcomeEmail(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value === '1' ? true : false,
        );
    }


    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::create($value)->format('F jS Y @ g:ia'),
        );
    }


    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::create($value)->format('F jS Y @ g:ia'),
        );
    }




}

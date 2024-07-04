<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Clients extends Model
{
    use HasFactory;


    protected $fillable = [
        "name",
        "email",
        "phone",
        "location",
        "quote",
        "details",
        "domains",
        "create_quote",
        "pro_emails",
        "hosting",
        "welcome_email_sent",
        "welcome_email_sent_count",
        "deposit",
        "payment_option",
        "status"
    ];

    public function payments(): HasMany {
        return $this->hasMany(Payment::class);
    }

}

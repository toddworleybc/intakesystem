<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


class Clients extends Model
{
    use HasFactory;


    protected $fillable = [
        "name",
        "email",
        "phone",
        "location",
        "details",
        "domains",
        "create_quote",
        "pro_emails",
        "welcome_email_sent_count",
        "payment_option",
        "ad_campaign_status",
        "status"
    ];

    // Casts ===============/

    protected function casts(): array
    {
        return [
            'domains'                  => 'array',
            'pro_emails'               => 'array',
            'welcome_email_sent_count' => 'array'
        ];
    }


    // Relations=======/

    public function payments(): HasMany {
        return $this->hasMany(Payment::class);
    }

    // Accessors and Mutators =========/

    protected function adCampaignStatus(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
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

    


    protected function domains(): Attribute
    {
        return Attribute::make(
            set: fn (array $value) => json_encode($value),
        );
    }

    protected function proEmails(): Attribute
    {
        return Attribute::make(
            set: fn (array $value) => json_encode($value),
        );
    }

    protected function welcomeEmailSentCount(): Attribute
    {
        return Attribute::make(
            set: fn (array $value) => json_encode($value),
        );
    }

}

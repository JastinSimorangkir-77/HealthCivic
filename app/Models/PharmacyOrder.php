<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyOrder extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'patient_name',
        'phone',
        'items',
        'delivery_address',
        'fulfillment',
        'status',
        'total',
        'consent_accepted',
        'confirmed_by_user_id',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'patient_name' => 'encrypted',
            'phone' => 'encrypted',
            'items' => 'encrypted:array',
            'delivery_address' => 'encrypted',
            'total' => 'integer',
            'consent_accepted' => 'boolean',
            'confirmed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }
}

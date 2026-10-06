<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'appointment_code',
        'patient_name',
        'phone',
        'bpjs_number',
        'service_type',
        'specialty',
        'doctor',
        'notes',
        'visit_date',
        'time_slot',
        'insurance',
        'status',
        'consent_accepted',
        'confirmed_by_user_id',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'patient_name' => 'encrypted',
            'phone' => 'encrypted',
            'bpjs_number' => 'encrypted',
            'notes' => 'encrypted',
            'visit_date' => 'date',
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

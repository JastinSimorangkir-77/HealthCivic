<?php

namespace App\Enums;

enum UserRole: string
{
    case Patient = 'patient';
    case Doctor = 'doctor';
    case Admin = 'admin';
    case PharmacyAdmin = 'pharmacy_admin';

    public function label(): string
    {
        return match ($this) {
            self::Patient => 'Pasien',
            self::Doctor => 'Dokter',
            self::Admin => 'Admin',
            self::PharmacyAdmin => 'Admin farmasi',
        };
    }
}

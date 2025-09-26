<?php

namespace App\Support;

use App\Models\Tenant;

class TenantContext
{
    private static ?Tenant $current = null;

    public static function set(?Tenant $tenant): void
    {
        self::$current = $tenant;
    }

    public static function get(): ?Tenant
    {
        return self::$current;
    }

    public static function id(): ?int
    {
        return self::$current?->id;
    }
}


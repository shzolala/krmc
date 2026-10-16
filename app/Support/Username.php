<?php

namespace App\Support;

use Illuminate\Support\Str;

class Username
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return Str::lower(trim($value));
    }

    public static function isValid(?string $value): bool
    {
        $normalized = self::normalize($value);

        return is_string($normalized) && (bool) preg_match('/^[a-z0-9](?:[a-z0-9._-]{1,28}[a-z0-9])?$/', $normalized);
    }

    public static function rule(): string
    {
        return 'regex:/^[a-z0-9](?:[a-z0-9._-]{1,28}[a-z0-9])?$/';
    }

    public static function routePattern(): string
    {
        return '[A-Za-z0-9][A-Za-z0-9._-]{1,28}[A-Za-z0-9]';
    }
}

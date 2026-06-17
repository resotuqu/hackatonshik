<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class FormatsDateTime
{
    public const LOCALE = 'ru';

    public static function parse(CarbonInterface|string|null $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return Carbon::instance($value)->timezone(config('app.timezone'));
        }

        return Carbon::parse($value)->timezone(config('app.timezone'));
    }

    public static function absolute(CarbonInterface|string|null $value): ?string
    {
        $date = self::parse($value);

        return $date?->locale(self::LOCALE)->format('d.m.Y H:i');
    }

    public static function date(CarbonInterface|string|null $value): ?string
    {
        $date = self::parse($value);

        return $date?->locale(self::LOCALE)->format('d.m.Y');
    }

    public static function relative(CarbonInterface|string|null $value): ?string
    {
        $date = self::parse($value);

        return $date?->locale(self::LOCALE)->diffForHumans();
    }

    public static function iso(CarbonInterface|string|null $value): ?string
    {
        $date = self::parse($value);

        return $date?->toIso8601String();
    }
}

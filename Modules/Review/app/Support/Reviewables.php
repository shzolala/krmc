<?php

namespace Modules\Review\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class Reviewables
{
    /**
     * @return array<string, class-string<Model>>
     */
    public static function map(): array
    {
        /** @var array<string, class-string<Model>> $map */
        $map = config('review.reviewables', []);

        return $map;
    }

    /**
     * @return list<class-string<Model>>
     */
    public static function classes(): array
    {
        return array_values(self::map());
    }

    public static function isSupported(Model $model): bool
    {
        return in_array($model::class, self::classes(), true);
    }

    public static function ensureSupported(Model $model): void
    {
        if (! self::isSupported($model)) {
            throw new InvalidArgumentException('این موجودیت قابل امتیازدهی نیست.');
        }
    }

    public static function aliasFor(Model $model): ?string
    {
        $alias = array_search($model::class, self::map(), true);

        return $alias === false ? null : $alias;
    }
}

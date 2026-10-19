<?php

namespace App\Enums;

enum PlanBadgeColor: string
{
    case Zinc = 'zinc';
    case Amber = 'amber';
    case Sky = 'sky';
    case Emerald = 'emerald';
    case Violet = 'violet';
    case Rose = 'rose';

    public function label(): string
    {
        return match ($this) {
            self::Zinc => 'خاکستری',
            self::Amber => 'کهربایی (طلایی)',
            self::Sky => 'آبی',
            self::Emerald => 'سبز',
            self::Violet => 'بنفش',
            self::Rose => 'سرخابی',
        };
    }

    /**
     * Soft tints keep the badge noticeable without shouting on light or dark surfaces.
     */
    public function classes(bool $onDark = false): string
    {
        if ($onDark) {
            return match ($this) {
                self::Zinc => 'bg-white/10 text-zinc-100 ring-white/25',
                self::Amber => 'bg-amber-400/15 text-amber-200 ring-amber-300/40',
                self::Sky => 'bg-sky-400/15 text-sky-200 ring-sky-300/40',
                self::Emerald => 'bg-emerald-400/15 text-emerald-200 ring-emerald-300/40',
                self::Violet => 'bg-violet-400/15 text-violet-200 ring-violet-300/40',
                self::Rose => 'bg-rose-400/15 text-rose-200 ring-rose-300/40',
            };
        }

        return match ($this) {
            self::Zinc => 'bg-zinc-100 text-zinc-700 ring-zinc-300/70 dark:bg-zinc-800 dark:text-zinc-200 dark:ring-zinc-600',
            self::Amber => 'bg-amber-50 text-amber-800 ring-amber-300/70 dark:bg-amber-950 dark:text-amber-200 dark:ring-amber-700',
            self::Sky => 'bg-sky-50 text-sky-800 ring-sky-300/70 dark:bg-sky-950 dark:text-sky-200 dark:ring-sky-700',
            self::Emerald => 'bg-emerald-50 text-emerald-800 ring-emerald-300/70 dark:bg-emerald-950 dark:text-emerald-200 dark:ring-emerald-700',
            self::Violet => 'bg-violet-50 text-violet-800 ring-violet-300/70 dark:bg-violet-950 dark:text-violet-200 dark:ring-violet-700',
            self::Rose => 'bg-rose-50 text-rose-800 ring-rose-300/70 dark:bg-rose-950 dark:text-rose-200 dark:ring-rose-700',
        };
    }

    public function accent(): string
    {
        return match ($this) {
            self::Zinc => 'border-t-zinc-400',
            self::Amber => 'border-t-amber-400',
            self::Sky => 'border-t-sky-400',
            self::Emerald => 'border-t-emerald-400',
            self::Violet => 'border-t-violet-400',
            self::Rose => 'border-t-rose-400',
        };
    }
}

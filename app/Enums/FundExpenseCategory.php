<?php

namespace App\Enums;

enum FundExpenseCategory: string
{
    case CoachTraining = 'coach_training';
    case TourismDevelopment = 'tourism_development';
    case Programs = 'programs';
    case Equipment = 'equipment';
    case SafetyRescue = 'safety_rescue';
    case Marketing = 'marketing';
    case Operations = 'operations';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CoachTraining => 'مربی‌پروری و آموزش',
            self::TourismDevelopment => 'توسعه گردشگری',
            self::Programs => 'برنامه‌ها و رویدادها',
            self::Equipment => 'تجهیزات',
            self::SafetyRescue => 'ایمنی و نجات',
            self::Marketing => 'تبلیغات و جذب سرمایه',
            self::Operations => 'هزینه‌های جاری و سایت',
            self::Other => 'سایر هزینه‌ها',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CoachTraining => 'bg-emerald-500',
            self::TourismDevelopment => 'bg-sky-500',
            self::Programs => 'bg-violet-500',
            self::Equipment => 'bg-amber-500',
            self::SafetyRescue => 'bg-red-500',
            self::Marketing => 'bg-pink-500',
            self::Operations => 'bg-slate-500',
            self::Other => 'bg-zinc-400',
        };
    }
}

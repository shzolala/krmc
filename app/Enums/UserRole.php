<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Climber = 'climber';
    case Club = 'club';
    case Coach = 'coach';
    case Leader = 'leader';
    case Shop = 'shop';
    case Organization = 'organization';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدیر',
            self::Climber => 'کوهنورد',
            self::Club => 'باشگاه',
            self::Coach => 'مربی',
            self::Leader => 'سرپرست',
            self::Shop => 'فروشگاه',
            self::Organization => 'ارگان',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'مدیریت کامل سایت: کاربران، محتوا، دایرکتوری و تعدیل نظرات.',
            self::Climber => 'کاربر عادی؛ مشاهده سایت، ثبت نظر و امتیاز.',
            self::Club => 'مالک باشگاه؛ ویرایش اطلاعات و پروفایل عمومی باشگاه.',
            self::Coach => 'مربی؛ ویرایش پروفایل تخصصی و نمایش در دایرکتوری مربیان.',
            self::Leader => 'سرپرست برنامه؛ ویرایش پروفایل و نمایش در دایرکتوری سرپرستان.',
            self::Shop => 'مالک فروشگاه؛ ویرایش اطلاعات فروشگاه در دایرکتوری.',
            self::Organization => 'ارگان یا نهاد؛ پنل اختصاصی و پروفایل عمومی.',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Admin => 'red',
            self::Climber => 'zinc',
            self::Club => 'blue',
            self::Coach => 'green',
            self::Leader => 'amber',
            self::Shop => 'purple',
            self::Organization => 'teal',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'shield-check',
            self::Climber => 'user',
            self::Club => 'building-office-2',
            self::Coach => 'academic-cap',
            self::Leader => 'flag',
            self::Shop => 'shopping-bag',
            self::Organization => 'building-library',
        };
    }

    public function hasPublicDirectoryProfile(): bool
    {
        return in_array($this, [
            self::Club,
            self::Coach,
            self::Leader,
            self::Shop,
            self::Organization,
        ], true);
    }
}

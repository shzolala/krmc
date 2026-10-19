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

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدیر',
            self::Climber => 'کوهنورد',
            self::Club => 'باشگاه',
            self::Coach => 'مربی',
            self::Leader => 'سرپرست',
            self::Shop => 'فروشگاه',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'مدیریت کامل سایت: کاربران، محتوا، دایرکتوری و تعدیل نظرات.',
            self::Climber => 'عضو عادی؛ پایه‌ی حساب همه‌ی کاربران (ثبت نظر، صعودها، عضویت و حمایت).',
            self::Club => 'مالک باشگاه؛ ویرایش اطلاعات و پروفایل عمومی باشگاه.',
            self::Coach => 'مربی؛ ویرایش پروفایل تخصصی و نمایش در دایرکتوری مربیان.',
            self::Leader => 'سرپرست برنامه؛ ویرایش پروفایل و نمایش در دایرکتوری سرپرستان.',
            self::Shop => 'مالک فروشگاه؛ ویرایش اطلاعات فروشگاه در دایرکتوری.',
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
        };
    }

    public function hasPublicDirectoryProfile(): bool
    {
        return in_array($this, [
            self::Club,
            self::Coach,
            self::Leader,
            self::Shop,
        ], true);
    }

    /**
     * The route name of this role's own dashboard section in the user panel.
     */
    public function dashboardRoute(): string
    {
        return 'panel.'.$this->value.'.dashboard';
    }

    /**
     * The route name of this role's profile editor, when the role owns an editable profile.
     */
    public function profileRoute(): ?string
    {
        return $this->hasPublicDirectoryProfile() ? 'panel.'.$this->value.'.profile' : null;
    }
}

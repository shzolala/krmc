<?php

namespace App\Support\Admin;

use App\Enums\UserRole;

class RoleCapabilities
{
    /**
     * @return list<array{label: string, roles: list<UserRole>}>
     */
    public static function matrix(): array
    {
        $everyone = UserRole::cases();

        return [
            ['label' => 'ورود به پنل کاربری اختصاصی', 'roles' => $everyone],
            ['label' => 'تنظیمات حساب و امنیت', 'roles' => $everyone],
            ['label' => 'ثبت نظر و امتیاز در سایت', 'roles' => $everyone],
            ['label' => 'پروفایل عمومی در دایرکتوری', 'roles' => array_values(array_filter(
                UserRole::cases(),
                fn (UserRole $role): bool => $role->hasPublicDirectoryProfile(),
            ))],
            ['label' => 'ویرایش اطلاعات باشگاه', 'roles' => [UserRole::Admin, UserRole::Club]],
            ['label' => 'ویرایش پروفایل مربی / سرپرست', 'roles' => [UserRole::Admin, UserRole::Coach, UserRole::Leader]],
            ['label' => 'ویرایش اطلاعات فروشگاه', 'roles' => [UserRole::Admin, UserRole::Shop]],
            ['label' => 'مدیریت مقاصد، جاذبه‌ها، اقامتگاه‌ها و پناهگاه‌ها', 'roles' => [UserRole::Admin]],
            ['label' => 'مدیریت برنامه‌ها و گزارش‌ها', 'roles' => [UserRole::Admin]],
            ['label' => 'تأیید یا رد نظرات کاربران', 'roles' => [UserRole::Admin]],
            ['label' => 'مدیریت کاربران و تغییر نقش‌ها', 'roles' => [UserRole::Admin]],
        ];
    }
}

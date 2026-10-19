<?php

namespace App\Support\Admin\Resources;

use App\Enums\PlanBadgeColor;
use App\Enums\UserRole;
use App\Models\MembershipPlan;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class MembershipPlanResource extends AdminResource
{
    public function model(): string
    {
        return MembershipPlan::class;
    }

    public function key(): string
    {
        return 'plans';
    }

    public function singular(): string
    {
        return 'پلن عضویت';
    }

    public function plural(): string
    {
        return 'پلن‌های عضویت';
    }

    public function icon(): string
    {
        return 'sparkles';
    }

    public function group(): string
    {
        return 'finance';
    }

    public function searchable(): array
    {
        return ['name', 'description', 'badge_label'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->audience;
    }

    public function filters(): array
    {
        return [
            'role' => ['label' => 'گروه', 'options' => $this->roleOptions()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام پلن'),
            Column::text('audience', 'گروه'),
            Column::number('level', 'سطح'),
            Column::money('price', 'قیمت سالانه'),
            Column::badge('badge_color', 'رنگ نشان'),
            Column::toggle('is_active', 'فعال'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات پلن' => [
                Field::text('name', 'نام پلن (مثلاً پلن نقره‌ای)')->required(),
                Field::select('role', 'مخصوص گروه', $this->roleOptions())
                    ->hint('خالی بگذارید تا پلن برای همه گروه‌ها نمایش داده شود.'),
                Field::number('level', 'سطح (۱ تا ۳)')->required()->rules(['between:1,3'])
                    ->hint('عدد بزرگ‌تر یعنی سطح بالاتر. عضو پس از ارتقا نمی‌تواند به سطح پایین‌تر برگردد.'),
                Field::number('price', 'قیمت پایه (تومان)')->required()
                    ->hint('قیمت برای «مدت پایه»؛ قیمت یک‌ماهه و شش‌ماهه از روی آن محاسبه می‌شود.'),
                Field::number('duration_days', 'مدت پایه (روز)')->default(365)->rules(['min:1', 'max:3650'])
                    ->hint('پیش‌فرض ۳۶۵ روز (سالانه).'),
                Field::textarea('description', 'توضیح کوتاه'),
                Field::tags('features', 'ویژگی‌ها و امکانات')->wide(),
                Field::toggle('is_active', 'فعال (قابل انتخاب توسط اعضا)'),
            ],
            'مدت‌های قابل انتخاب و تخفیف' => [
                Field::toggle('allow_monthly', 'اجازه انتخاب یک‌ماهه'),
                Field::toggle('allow_semiannual', 'اجازه انتخاب شش‌ماهه'),
                Field::toggle('allow_annual', 'اجازه انتخاب یک‌ساله'),
                Field::number('discount_semiannual', 'تخفیف شش‌ماهه (٪)')->default(0)->rules(['max:90']),
                Field::number('discount_annual', 'تخفیف یک‌ساله (٪)')->default(0)->rules(['max:90']),
            ],
            'نشان سطح' => [
                Field::text('badge_label', 'متن نشان')->hint('در صورت خالی بودن، نام پلن نمایش داده می‌شود.'),
                Field::select('badge_color', 'رنگ نشان', $this->enumOptions(PlanBadgeColor::class))->required(),
                Field::select('badge_icon', 'آیکون نشان', array_combine(MembershipPlan::BADGE_ICONS, MembershipPlan::BADGE_ICONS))->required(),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function roleOptions(): array
    {
        return collect(UserRole::cases())
            ->reject(fn (UserRole $role): bool => $role === UserRole::Admin)
            ->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])
            ->all();
    }
}

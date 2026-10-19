<?php

namespace App\Support\Admin\Resources;

use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MembershipDueResource extends AdminResource
{
    public function model(): string
    {
        return MembershipDue::class;
    }

    public function key(): string
    {
        return 'dues';
    }

    public function singular(): string
    {
        return 'حق عضویت';
    }

    public function plural(): string
    {
        return 'حق عضویت اعضا';
    }

    public function icon(): string
    {
        return 'identification';
    }

    public function group(): string
    {
        return 'finance';
    }

    public function titleAttribute(): string
    {
        return 'title';
    }

    public function searchable(): array
    {
        return ['title', 'period'];
    }

    public function with(): array
    {
        return ['user:id,name', 'plan:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->user?->name;
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'عنوان'),
            Column::text('user.name', 'عضو'),
            Column::text('plan.name', 'پلن'),
            Column::text('period', 'دوره'),
            Column::money('amount', 'مبلغ'),
            Column::number('term_months', 'ماه'),
            Column::date('ends_on', 'پایان اعتبار'),
        ];
    }

    public function fields(): array
    {
        return [
            'تعهد عضویت' => [
                Field::relation('user_id', 'عضو (کوهنورد، باشگاه، مربی، سرپرست، فروشگاه)', User::class)->required(),
                Field::text('period', 'دوره (مثلاً ۱۴۰۵)', 20)->required(),
                Field::relation('membership_plan_id', 'پلن عضویت', MembershipPlan::class)
                    ->hint('با انتخاب پلن، مبلغ خودکار از قیمت پلن پر می‌شود.'),
                Field::number('amount', 'مبلغ حق عضویت (تومان)')
                    ->rules(['required_without:membership_plan_id'])
                    ->hint('برای مبلغ سفارشی پر کنید؛ در غیر این صورت قیمت پلن اعمال می‌شود.'),
                Field::select('term_months', 'مدت عضویت', MembershipPlan::TERM_LABELS_STRING),
                Field::date('starts_on', 'شروع اعتبار'),
                Field::date('ends_on', 'پایان اعتبار')->hint('در صورت خالی بودن و داشتن تاریخ شروع، از روی مدت محاسبه می‌شود.'),
                Field::date('due_date', 'سررسید پرداخت'),
                Field::text('title', 'عنوان')->hint('در صورت خالی بودن، از دوره و نام عضو ساخته می‌شود.')
                    ->autoValue(fn (array $data): string => 'حق عضویت '.($data['period'] ?? '').' — '.User::query()->find($data['user_id'] ?? null)?->name),
                Field::textarea('note', 'یادداشت'),
            ],
        ];
    }

    public function prepare(array $validated, ?Model $record): array
    {
        $data = parent::prepare($validated, $record);

        $months = (int) ($data['term_months'] ?: 12);
        $data['term_months'] = $months;
        $plan = $data['membership_plan_id'] !== null ? MembershipPlan::query()->find($data['membership_plan_id']) : null;

        if ($plan !== null) {
            $term = $plan->termPrice($months);
            $data['amount'] ??= $term['final'];
            $data['discount_percent'] = $term['discount'];
        }

        if ($data['ends_on'] === null && $data['starts_on'] !== null) {
            $data['ends_on'] = Carbon::parse($data['starts_on'])->addDays(MembershipPlan::TERM_DAYS[$months] ?? 365)->toDateString();
        }

        return $data;
    }
}

<?php

namespace App\Support\Admin\Resources;

use App\Enums\FundIncomeSource;
use App\Models\FundCampaign;
use App\Models\FundIncome;
use App\Models\MembershipDue;
use App\Models\Sponsor;
use App\Models\SupportProject;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class FundIncomeResource extends AdminResource
{
    public function model(): string
    {
        return FundIncome::class;
    }

    public function key(): string
    {
        return 'incomes';
    }

    public function singular(): string
    {
        return 'دریافتی';
    }

    public function plural(): string
    {
        return 'دریافتی‌های صندوق';
    }

    public function icon(): string
    {
        return 'arrow-down-tray';
    }

    public function group(): string
    {
        return 'finance';
    }

    public function titleAttribute(): string
    {
        return 'payer_name';
    }

    public function recordTitle(Model $record): string
    {
        return $record->payerLabel().' — '.fa_money($record->amount);
    }

    public function searchable(): array
    {
        return ['payer_name', 'note'];
    }

    public function with(): array
    {
        return ['user:id,name', 'campaign:id,title', 'sponsor:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->campaign?->title;
    }

    public function filters(): array
    {
        return [
            'source' => ['label' => 'منبع', 'options' => $this->enumOptions(FundIncomeSource::class)],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('payer_name', 'پرداخت‌کننده', false),
            Column::badge('source', 'منبع'),
            Column::money('amount', 'مبلغ'),
            Column::toggle('is_in_kind', 'غیرنقدی'),
            Column::date('received_at', 'تاریخ دریافت'),
        ];
    }

    public function fields(): array
    {
        return [
            'دریافتی' => [
                Field::select('source', 'منبع درآمد', $this->enumOptions(FundIncomeSource::class))->required(),
                Field::number('amount', 'مبلغ (تومان)')->required(),
                Field::date('received_at', 'تاریخ دریافت')->required(),
            ],
            'پرداخت‌کننده' => [
                Field::relation('user_id', 'عضو سایت', User::class),
                Field::text('payer_name', 'نام پرداخت‌کننده (اسپانسر / خیّر)')->hint('اگر پرداخت‌کننده عضو سایت نیست، نام او را بنویسید.'),
                Field::relation('membership_due_id', 'تعهد حق عضویت', MembershipDue::class, 'title')
                    ->rules(['required_if:source,membership'])
                    ->hint('برای دریافتی از نوع «حق عضویت» الزامی است.'),
                Field::toggle('is_in_kind', 'حمایت غیرنقدی (کالا یا خدمات – تهاتر)'),
                Field::text('in_kind_description', 'شرح کالا یا خدمات')->hint('در صورت غیرنقدی بودن لازم است؛ «مبلغ» را معادل ریالی بگذارید.')
                    ->rules(['required_if:is_in_kind,1']),
                Field::number('installment_no', 'نوبت حمایت')->hint('مثلاً ۱ برای نوبت اول و ۲ برای نوبت دوم.')->rules(['between:1,50']),
                Field::relation('support_project_id', 'طرح حمایتی', SupportProject::class, 'title'),
                Field::relation('sponsor_id', 'حامی کوهستان (برای اسپانسرشیپ)', Sponsor::class),
                Field::relation('fund_campaign_id', 'کمپین جذب سرمایه', FundCampaign::class, 'title'),
                Field::textarea('note', 'یادداشت'),
            ],
        ];
    }

    public function prepare(array $validated, ?Model $record): array
    {
        $data = parent::prepare($validated, $record);

        if ($data['membership_due_id'] !== null) {
            $data['user_id'] = MembershipDue::query()->find($data['membership_due_id'])?->user_id ?? $data['user_id'];
        }

        return $data;
    }
}

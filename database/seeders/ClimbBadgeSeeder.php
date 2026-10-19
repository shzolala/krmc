<?php

namespace Database\Seeders;

use App\Enums\PlanBadgeColor;
use App\Models\ClimbBadge;
use App\Models\Destination;
use App\Models\PeakAscent;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds the peak-ladder badges and sample ascents for demo climbers. Safe to re-run.
 */
class ClimbBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['اولین قله', 'نخستین پله‌ی نردبان؛ شروع یک مسیر بلند.', 1, null, PlanBadgeColor::Zinc, 'flag'],
            ['قله‌نورد', 'پنج قله را پشت سر گذاشته‌اید.', 5, null, PlanBadgeColor::Sky, 'shield-check'],
            ['سه هزارنورد', 'ده قله‌ی بالای ۳۰۰۰ متر.', 10, 3000, PlanBadgeColor::Emerald, 'bolt'],
            ['چهار هزارنورد', 'نخستین قله‌ی بالای ۴۰۰۰ متر.', 1, 4000, PlanBadgeColor::Violet, 'sparkles'],
            ['کوهنورد پیشرفته', 'پانزده قله؛ تجربه‌ی واقعی کوهستان.', 15, null, PlanBadgeColor::Rose, 'fire'],
            ['فاتح بلندی‌ها', 'پنج قله‌ی بالای ۴۰۰۰ متر.', 5, 4000, PlanBadgeColor::Amber, 'trophy'],
        ];

        foreach ($badges as $order => [$name, $description, $count, $elevation, $color, $icon]) {
            ClimbBadge::query()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'required_count' => $count,
                    'min_elevation' => $elevation,
                    'color' => $color,
                    'icon' => $icon,
                    'sort_order' => $order,
                    'is_active' => true,
                ],
            );
        }

        $this->seedSampleAscents();
    }

    private function seedSampleAscents(): void
    {
        $peaks = Destination::query()->peaks()->whereNotNull('elevation')->orderBy('id')->get();

        if ($peaks->isEmpty()) {
            return;
        }

        foreach (['climber' => 7, 'sample-climber-gold' => 18, 'sample-climber-basic' => 2, 'sample-coach-silver' => 12] as $username => $count) {
            $user = User::query()->where('username', $username)->first();

            if ($user === null) {
                continue;
            }

            $user->peakAscents()->delete();

            for ($step = 0; $step < min($count, $peaks->count()); $step++) {
                PeakAscent::query()->create([
                    'user_id' => $user->id,
                    'destination_id' => $peaks[($step * 5) % $peaks->count()]->id,
                    'climbed_on' => now()->subDays($step * 17 + 5)->toDateString(),
                ]);
            }
        }
    }
}

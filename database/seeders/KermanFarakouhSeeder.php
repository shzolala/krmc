<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;

class KermanFarakouhSeeder extends Seeder
{
    /**
     * @var array<string, array{0: float, 1: float}>
     */
    private const CITY_COORDINATES = [
        'کرمان' => [30.2839000, 57.0834000],
        'سیرجان' => [29.4514000, 55.6814000],
        'رفسنجان' => [30.4067000, 55.9939000],
        'جیرفت' => [28.6751000, 57.7372000],
        'بم' => [29.1060000, 58.3570000],
        'کهنوج' => [27.8717000, 57.7086000],
        'ماهان' => [30.0611000, 57.2903000],
        'تهران' => [35.6892000, 51.3890000],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubsPayload = $this->loadJson('kerman_clubs.json');
        $coachesPayload = $this->loadJson('kerman_coaches.json');

        /** @var array<int, array<string, mixed>> $clubRows */
        $clubRows = $clubsPayload['clubs'] ?? [];
        /** @var array<int, array<string, mixed>> $coachRows */
        $coachRows = $coachesPayload['coaches'] ?? [];

        $clubsByName = collect();

        foreach ($clubRows as $index => $row) {
            $club = $this->seedClub($row, $index);
            $clubsByName->put($club->name, $club);
        }

        foreach ($coachRows as $index => $row) {
            $clubName = trim((string) ($row['club'] ?? ''));
            $club = $clubName !== ''
                ? $clubsByName->get($clubName) ?? $this->findClubByName($clubName, $clubsByName)
                : null;

            $this->seedCoach($row, $club, $index);
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function seedClub(array $row, int $index): Club
    {
        $farakouhId = (int) $row['id'];
        $name = (string) $row['name'];
        $city = $this->inferCity($name, (string) ($row['province'] ?? 'کرمان'));
        [$latitude, $longitude] = $this->coordinatesFor($city, $index);

        $owner = null;
        $managerName = $row['manager'] ?? null;

        if (is_string($managerName) && $managerName !== '') {
            $owner = User::query()->updateOrCreate(
                ['username' => 'club-'.$farakouhId],
                [
                    'name' => $managerName,
                    'mobile' => $this->mobileFromId(200000000 + $farakouhId),
                    'role' => UserRole::Club,
                    'password' => 'password',
                ]
            );
        }

        $summaryParts = array_filter([
            $managerName ? 'مدیر: '.$managerName : null,
            isset($row['education_head']) && is_string($row['education_head']) && $row['education_head'] !== ''
                ? 'مسئول آموزش: '.$row['education_head']
                : null,
            'منبع: فراکوه',
        ]);

        $club = Club::query()->updateOrCreate(
            ['farakouh_id' => $farakouhId],
            [
                'name' => $name,
                'slug' => 'club-'.$farakouhId,
                'summary' => implode(' · ', $summaryParts) ?: 'باشگاه کوهنوردی استان کرمان',
                'description' => $this->clubDescription($row),
                'city' => $city,
                'province' => (string) ($row['province'] ?? 'کرمان'),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $city.'، استان '.($row['province'] ?? 'کرمان'),
                'manager' => is_string($managerName) ? $managerName : null,
                'education_head' => is_string($row['education_head'] ?? null) ? $row['education_head'] : null,
                'avg_rating' => $this->nullableInt($row['avg_rating'] ?? null),
                'contact' => $row['contact'] ?? null,
                'courses_summary' => $row['courses_summary'] ?? null,
                'farakouh_url' => is_string($row['url'] ?? null) ? $row['url'] : null,
                'member_count' => (int) ($row['members_count'] ?? 0),
                'program_count' => (int) ($row['total_courses_held'] ?? 0),
                'is_verified' => true,
                'is_featured' => $index < 8,
                'is_premium' => ((int) ($row['total_courses_held'] ?? 0)) >= 50,
                'view_count' => max(50, (int) ($row['members_count'] ?? 0) * 3),
                'owner_id' => $owner?->id,
            ]
        );

        if ($owner !== null) {
            $owner->update(['primary_club_id' => $club->id]);
            $club->members()->syncWithoutDetaching([
                $owner->id => ['status' => 'active'],
            ]);
        }

        return $club;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function seedCoach(array $row, ?Club $club, int $index): CoachProfile
    {
        $farakouhId = (int) $row['id'];
        $name = (string) $row['name'];
        $specialty = $this->specialtyFromCertificates($row['certificates'] ?? []);
        $validCertificates = collect($row['certificates'] ?? [])
            ->filter(fn (mixed $certificate): bool => is_array($certificate) && ($certificate['valid'] ?? false) === true)
            ->count();

        $user = User::query()->updateOrCreate(
            ['username' => 'coach-'.$farakouhId],
            [
                'name' => $name,
                'mobile' => $this->mobileFromId(300000000 + $farakouhId),
                'role' => UserRole::Coach,
                'primary_club_id' => $club?->id,
                'password' => 'password',
            ]
        );

        if ($club !== null) {
            $club->members()->syncWithoutDetaching([
                $user->id => ['status' => 'active'],
            ]);
        }

        return CoachProfile::query()->updateOrCreate(
            ['farakouh_id' => $farakouhId],
            [
                'user_id' => $user->id,
                'club_id' => $club?->id,
                'slug' => 'coach-'.$farakouhId,
                'headline' => $specialty.($club ? ' · '.$club->name : ''),
                'bio' => $this->coachBio($row, $club),
                'specialty' => $specialty,
                'province' => (string) ($row['province'] ?? 'کرمان'),
                'avg_rating' => $this->nullableInt($row['avg_rating'] ?? null),
                'total_courses_taught' => (int) ($row['total_courses_taught'] ?? 0),
                'certificates' => $row['certificates'] ?? [],
                'contact' => $row['contact'] ?? null,
                'courses_summary' => $row['courses_summary'] ?? null,
                'farakouh_url' => is_string($row['url'] ?? null) ? $row['url'] : null,
                'experience_years' => min(40, max(1, (int) ceil(((int) ($row['total_courses_taught'] ?? 0)) / 5))),
                'is_verified' => $validCertificates > 0,
                'is_featured' => $index < 10 || ((int) ($row['total_courses_taught'] ?? 0)) >= 20,
                'is_premium' => ((int) ($row['total_courses_taught'] ?? 0)) >= 40,
                'is_leader' => false,
            ]
        );
    }

    /**
     * Resolve a club already in the Kerman catalog. Out-of-province or
     * otherwise unknown club names stay unlinked instead of inventing rows.
     *
     * @param  Collection<string, Club>  $clubsByName
     */
    private function findClubByName(string $name, Collection $clubsByName): ?Club
    {
        $existing = Club::query()->where('name', $name)->first();

        if ($existing !== null) {
            $clubsByName->put($name, $existing);
        }

        return $existing;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function clubDescription(array $row): string
    {
        $lines = [
            'پروفایل باشگاه بر اساس دادهٔ رسمی فراکوه.',
            'شناسه فراکوه: '.(string) $row['id'],
        ];

        if (! empty($row['url'])) {
            $lines[] = 'صفحه منبع: '.$row['url'];
        }

        if (! empty($row['manager'])) {
            $lines[] = 'مدیر: '.$row['manager'];
        }

        if (! empty($row['education_head'])) {
            $lines[] = 'مسئول آموزش: '.$row['education_head'];
        }

        $summary = $row['courses_summary'] ?? null;

        if (is_array($summary)) {
            if (! empty($summary['latest_title'])) {
                $lines[] = 'آخرین دوره: '.$summary['latest_title'].' ('.($summary['latest_date_jalali'] ?? $summary['latest_date'] ?? '—').')';
            }

            if (! empty($summary['top_course_types']) && is_array($summary['top_course_types'])) {
                $top = collect($summary['top_course_types'])
                    ->map(fn (mixed $count, mixed $title): string => $title.' ('.$count.')')
                    ->take(5)
                    ->implode('، ');

                if ($top !== '') {
                    $lines[] = 'انواع دوره پرتکرار: '.$top;
                }
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function coachBio(array $row, ?Club $club): string
    {
        $lines = [
            'پروفایل مربی بر اساس دادهٔ رسمی فراکوه.',
            'شناسه فراکوه: '.(string) $row['id'],
        ];

        if ($club !== null) {
            $lines[] = 'باشگاه ثبت‌کننده: '.$club->name.' (شناسه باشگاه: '.$club->id.')';
        } elseif (! empty($row['club'])) {
            $lines[] = 'باشگاه ثبت‌کننده (نام خام): '.$row['club'];
        }

        if (! empty($row['url'])) {
            $lines[] = 'صفحه منبع: '.$row['url'];
        }

        $certificates = collect($row['certificates'] ?? [])
            ->filter(fn (mixed $certificate): bool => is_array($certificate))
            ->map(function (array $certificate): string {
                $status = ($certificate['valid'] ?? false) ? 'معتبر' : 'منقضی';
                $until = $certificate['valid_until_jalali'] ?? '—';

                return ($certificate['title'] ?? 'گواهی')." تا {$until} ({$status})";
            })
            ->implode("\n- ");

        if ($certificates !== '') {
            $lines[] = "گواهینامه‌ها:\n- ".$certificates;
        }

        $summary = $row['courses_summary'] ?? null;

        if (is_array($summary) && ! empty($summary['latest_title'])) {
            $lines[] = 'آخرین دوره تدریس‌شده: '.$summary['latest_title'].' ('.($summary['latest_date_jalali'] ?? '—').')';
        }

        return implode("\n", $lines);
    }

    private function specialtyFromCertificates(mixed $certificates): string
    {
        if (! is_array($certificates) || $certificates === []) {
            return 'کوهنوردی';
        }

        $titles = collect($certificates)
            ->filter(fn (mixed $certificate): bool => is_array($certificate) && ! empty($certificate['title']))
            ->map(fn (array $certificate): string => (string) $certificate['title']);

        $preferred = $titles->first(
            fn (string $title): bool => str_contains($title, 'مربیگری')
        );

        return $preferred ?? $titles->first() ?? 'کوهنوردی';
    }

    private function inferCity(string $name, string $fallbackProvince): string
    {
        foreach (array_keys(self::CITY_COORDINATES) as $city) {
            if (str_contains($name, $city)) {
                return $city;
            }
        }

        return $fallbackProvince === 'تهران' ? 'تهران' : 'کرمان';
    }

    /**
     * @return array{0: float, 1: float}
     */
    private function coordinatesFor(string $city, int $offset): array
    {
        [$latitude, $longitude] = self::CITY_COORDINATES[$city] ?? self::CITY_COORDINATES['کرمان'];

        $delta = ($offset % 20) * 0.004;

        return [
            round($latitude + $delta, 7),
            round($longitude + (($offset % 7) * 0.003), 7),
        ];
    }

    private function mobileFromId(int $seed): string
    {
        return '09'.str_pad((string) ($seed % 1000000000), 9, '0', STR_PAD_LEFT);
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $filename): array
    {
        $path = storage_path($filename);

        if (! File::exists($path)) {
            throw new RuntimeException("Missing seed data file: {$path}");
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}

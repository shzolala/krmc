<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\MountainRegion;
use App\Models\Program;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * Seeds the home page slider with the locally stored photos in public/images.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'جامعه کوهنوردان کرمان',
                'subtitle' => 'برنامه‌های صعود معتبر کرمان، با باشگاه و سرپرست تأییدشده؛ از قله‌های برفی هزار تا کویر لوت.',
                'image_path' => 'images/covers/hero.jpg',
                'program_id' => Program::query()->where('slug', 'program-hezar-day')->value('id'),
            ],
            [
                'title' => 'بالای ابرهای هزار',
                'subtitle' => 'بلندترین قله‌های استان در کوهستان هزار؛ ارتفاع، سختی مسیر و فصل مناسب را ببینید.',
                'image_path' => 'images/slides/hezar.jpg',
                'mountain_region_id' => MountainRegion::query()->where('slug', 'hezar-hazaran-massif')->value('id'),
            ],
            [
                'title' => 'کلوت‌ها و شن‌های لوت',
                'subtitle' => 'طبیعت‌گردی و شب‌مانی در دشت لوت، ثبت‌شده در فهرست میراث جهانی یونسکو.',
                'image_path' => 'images/slides/lut-01.jpg',
                'mountain_region_id' => MountainRegion::query()->where('slug', 'lut-desert-shahdad-kaluts')->value('id'),
            ],
            [
                'title' => 'تپه‌های روان شهداد',
                'subtitle' => 'مسیرهای آفرود و پیاده در دل کویر، همراه با راهنمای محلی.',
                'image_path' => 'images/slides/lut-02.jpg',
                'destination_id' => Destination::query()->where('slug', 'shahdad-kaluts-yardangs')->value('id'),
            ],
            [
                'title' => 'نمک و گز در حاشیه لوت',
                'subtitle' => 'کویر فقط شن نیست؛ دشت‌های نمکی و درختان گز با قله‌های برفی در افق.',
                'image_path' => 'images/slides/lut-03.jpg',
                'destination_id' => Destination::query()->where('slug', 'lut-desert-dasht-e-lut')->value('id'),
            ],
        ];

        foreach ($slides as $index => $slide) {
            HeroSlide::query()->updateOrCreate(
                ['image_path' => $slide['image_path']],
                [...$slide, 'sort_order' => $index + 1, 'is_active' => true],
            );
        }
    }
}

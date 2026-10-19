<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\MountainRegion;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroSlideTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_active_slides_in_order_with_internal_caption_links(): void
    {
        $program = Program::factory()->create(['title' => 'برنامه اسلاید']);
        $region = MountainRegion::factory()->create(['name' => 'منطقه اسلاید']);

        HeroSlide::factory()->create(['title' => 'اسلاید دوم', 'sort_order' => 2, 'mountain_region_id' => $region->id]);
        HeroSlide::factory()->create(['title' => 'اسلاید اول', 'sort_order' => 1, 'program_id' => $program->id]);
        HeroSlide::factory()->create(['title' => 'اسلاید خاموش', 'sort_order' => 0, 'is_active' => false]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('اسلاید خاموش', $html);
        $this->assertLessThan(strpos($html, 'اسلاید دوم'), strpos($html, 'اسلاید اول'));
        $this->assertStringContainsString('href="'.route('programs.show', $program).'"', $html);
        $this->assertStringContainsString('برنامه اسلاید', $html);
        $this->assertStringContainsString('href="'.route('regions.show', $region).'"', $html);
        $this->assertStringContainsString('data-hero-dot', $html);
    }

    public function test_home_page_falls_back_to_a_default_slide_without_slides(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('جامعه کوهنوردان کرمان', false)
            ->assertDontSee('data-hero-dot', false);
    }

    public function test_slide_target_prefers_program_then_destination_then_region(): void
    {
        $destination = Destination::factory()->create();
        $region = MountainRegion::factory()->create();
        $program = Program::factory()->create();

        $slide = HeroSlide::factory()->create(['mountain_region_id' => $region->id, 'destination_id' => $destination->id]);
        $this->assertSame(route('destinations.show', $destination), $slide->target()['url']);

        $slide->update(['program_id' => $program->id]);
        $this->assertSame(route('programs.show', $program), $slide->fresh()->target()['url']);

        $this->assertNull(HeroSlide::factory()->create()->target());
    }

    public function test_admin_uploads_replaces_and_keeps_slide_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'hero-slides'), [
            'title' => 'اسلاید جدید',
            'image_path' => UploadedFile::fake()->image('first.jpg', 1600, 700),
            'is_active' => '1',
        ])->assertRedirect(route('panel.admin.resources.index', 'hero-slides'));

        $slide = HeroSlide::query()->firstOrFail();
        Storage::disk('public')->assertExists($slide->image_path);
        $first = $slide->image_path;

        $this->actingAs($admin)->put(route('panel.admin.resources.update', ['resource' => 'hero-slides', 'id' => $slide->id]), [
            'title' => 'عنوان تازه',
            'is_active' => '1',
        ])->assertRedirect();
        $this->assertSame($first, $slide->fresh()->image_path);

        $this->actingAs($admin)->put(route('panel.admin.resources.update', ['resource' => 'hero-slides', 'id' => $slide->id]), [
            'title' => 'عنوان تازه',
            'image_path' => UploadedFile::fake()->image('second.png', 1600, 700),
            'is_active' => '1',
        ])->assertRedirect();

        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($slide->fresh()->image_path);
    }

    public function test_slide_requires_an_image_and_rejects_non_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'hero-slides'), ['title' => 'بدون تصویر'])
            ->assertSessionHasErrors('image_path');

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'hero-slides'), [
            'title' => 'فایل نادرست',
            'image_path' => UploadedFile::fake()->create('note.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('image_path');
    }
}

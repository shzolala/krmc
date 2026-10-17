<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mountain_regions', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_id')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->json('alt_names')->nullable();
            $table->string('type');
            $table->string('province')->nullable();
            $table->json('counties')->nullable();
            $table->string('nearest_city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('bbox')->nullable();
            $table->unsignedInteger('highest_elevation')->nullable();
            $table->string('highest_peak_catalog_id')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->json('seo')->nullable();
            $table->json('notes')->nullable();
            $table->json('data_confidence')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_featured']);
            $table->index('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mountain_regions');
    }
};

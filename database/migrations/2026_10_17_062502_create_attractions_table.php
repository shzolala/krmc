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
        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_id')->unique();
            $table->foreignId('mountain_region_id')->nullable()->constrained('mountain_regions')->nullOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->json('alt_names')->nullable();
            $table->string('type');
            $table->json('types')->nullable();
            $table->string('era')->nullable();
            $table->json('unesco')->nullable();
            $table->string('province')->nullable();
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('coordinate_meta')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->json('visiting')->nullable();
            $table->json('extras')->nullable();
            $table->json('seo')->nullable();
            $table->json('sources')->nullable();
            $table->json('data_confidence')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_featured']);
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};

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
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('catalog_id')->nullable()->unique()->after('id');
            $table->foreignId('mountain_region_id')->nullable()->after('catalog_id')->constrained('mountain_regions')->nullOnDelete();
            $table->string('category')->nullable()->after('slug');
            $table->string('subtype')->nullable()->after('category');
            $table->string('name_en')->nullable()->after('name');
            $table->json('alt_names')->nullable()->after('name_en');
            $table->string('province')->nullable()->after('region');
            $table->string('county')->nullable()->after('province');
            $table->string('nearest_city')->nullable()->after('county');
            $table->string('nearest_village')->nullable()->after('nearest_city');
            $table->json('coordinate_meta')->nullable()->after('longitude');
            $table->boolean('description_is_short')->default(false)->after('description');
            $table->json('trail_info')->nullable()->after('description_is_short');
            $table->json('extras')->nullable()->after('trail_info');
            $table->json('seo')->nullable()->after('extras');
            $table->json('sources')->nullable()->after('seo');
            $table->json('data_confidence')->nullable()->after('sources');

            $table->index(['category', 'is_featured']);
            $table->index('mountain_region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mountain_region_id');
            $table->dropColumn([
                'catalog_id',
                'category',
                'subtype',
                'name_en',
                'alt_names',
                'province',
                'county',
                'nearest_city',
                'nearest_village',
                'coordinate_meta',
                'description_is_short',
                'trail_info',
                'extras',
                'seo',
                'sources',
                'data_confidence',
            ]);
        });
    }
};

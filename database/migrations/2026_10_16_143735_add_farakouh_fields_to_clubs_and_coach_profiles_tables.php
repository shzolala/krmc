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
        Schema::table('clubs', function (Blueprint $table) {
            $table->unsignedInteger('farakouh_id')->nullable()->unique()->after('id');
            $table->string('farakouh_url')->nullable()->after('farakouh_id');
            $table->string('province')->nullable()->after('city');
            $table->string('manager')->nullable()->after('address');
            $table->string('education_head')->nullable()->after('manager');
            $table->unsignedSmallInteger('avg_rating')->nullable()->after('education_head');
            $table->json('contact')->nullable()->after('avg_rating');
            $table->json('courses_summary')->nullable()->after('contact');
        });

        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->unsignedInteger('farakouh_id')->nullable()->unique()->after('id');
            $table->string('farakouh_url')->nullable()->after('farakouh_id');
            $table->string('province')->nullable()->after('specialty');
            $table->unsignedSmallInteger('avg_rating')->nullable()->after('province');
            $table->unsignedInteger('total_courses_taught')->default(0)->after('avg_rating');
            $table->json('certificates')->nullable()->after('total_courses_taught');
            $table->json('contact')->nullable()->after('certificates');
            $table->json('courses_summary')->nullable()->after('contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn([
                'farakouh_id',
                'farakouh_url',
                'province',
                'manager',
                'education_head',
                'avg_rating',
                'contact',
                'courses_summary',
            ]);
        });

        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'farakouh_id',
                'farakouh_url',
                'province',
                'avg_rating',
                'total_courses_taught',
                'certificates',
                'contact',
                'courses_summary',
            ]);
        });
    }
};

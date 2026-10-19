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
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedBigInteger('price');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('badge_label')->nullable();
            $table->string('badge_color')->default('zinc');
            $table->string('badge_icon')->default('star');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['role', 'is_active', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};

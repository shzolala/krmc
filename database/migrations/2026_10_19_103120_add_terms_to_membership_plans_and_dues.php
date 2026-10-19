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
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_days')->default(365)->after('price');
            $table->boolean('allow_monthly')->default(false)->after('duration_days');
            $table->boolean('allow_semiannual')->default(false)->after('allow_monthly');
            $table->boolean('allow_annual')->default(true)->after('allow_semiannual');
            $table->unsignedTinyInteger('discount_semiannual')->default(0)->after('allow_annual');
            $table->unsignedTinyInteger('discount_annual')->default(0)->after('discount_semiannual');
        });

        Schema::table('membership_dues', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique(['user_id', 'period']);
            $table->unsignedTinyInteger('term_months')->default(12)->after('amount');
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('term_months');
            $table->date('starts_on')->nullable()->after('discount_percent');
            $table->date('ends_on')->nullable()->after('starts_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_dues', function (Blueprint $table) {
            $table->dropColumn(['term_months', 'discount_percent', 'starts_on', 'ends_on']);
            $table->unique(['user_id', 'period']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn(['duration_days', 'allow_monthly', 'allow_semiannual', 'allow_annual', 'discount_semiannual', 'discount_annual']);
        });
    }
};

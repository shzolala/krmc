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
        Schema::table('fund_incomes', function (Blueprint $table) {
            $table->foreignId('support_project_id')->nullable()->after('sponsor_id')->constrained()->nullOnDelete();
            $table->boolean('is_in_kind')->default(false)->after('amount');
            $table->string('in_kind_description')->nullable()->after('is_in_kind');
            $table->unsignedTinyInteger('installment_no')->nullable()->after('in_kind_description');
        });

        Schema::table('fund_expenses', function (Blueprint $table) {
            $table->foreignId('support_project_id')->nullable()->after('fund_campaign_id')->constrained()->nullOnDelete();
        });

        Schema::table('sponsors', function (Blueprint $table) {
            $table->string('preferred_category')->nullable()->after('support_note');
            $table->foreignId('support_project_id')->nullable()->after('preferred_category')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('support_project_id');
            $table->dropColumn('preferred_category');
        });

        Schema::table('fund_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('support_project_id');
        });

        Schema::table('fund_incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('support_project_id');
            $table->dropColumn(['is_in_kind', 'in_kind_description', 'installment_no']);
        });
    }
};

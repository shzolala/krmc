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
        Schema::create('fund_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->unsignedBigInteger('amount');
            $table->date('received_at');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('membership_due_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fund_campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payer_name')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['source', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_incomes');
    }
};

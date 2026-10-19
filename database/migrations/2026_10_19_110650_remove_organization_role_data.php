<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The "organization" role no longer exists: its plans and access grants are dropped
     * and its accounts become regular climbers.
     */
    public function up(): void
    {
        DB::table('membership_plans')->where('role', 'organization')->delete();
        DB::table('finance_ledger_accesses')->where('role', 'organization')->delete();
        DB::table('users')->where('role', 'organization')->update(['role' => 'climber']);
    }

    public function down(): void
    {
        //
    }
};

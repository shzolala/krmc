<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A person is one account that may hold several roles at once (club owner, coach, shop owner, ...).
     * Those roles are now derived from what the account owns, so the single "role" column is replaced by
     * a flag that only marks the separate administrator account type.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(false)->after('email')->index();
        });

        DB::table('users')->where('role', 'admin')->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('climber')->after('email');
        });

        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_admin');
        });
    }
};

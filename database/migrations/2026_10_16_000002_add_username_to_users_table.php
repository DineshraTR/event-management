<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->unique()->after('name');
            });

            // Set default usernames for existing users based on email prefix
            DB::table('users')->where('email', 'admin@eventpro.com')->update(['username' => 'admin']);
            DB::table('users')->where('email', 'manager@eventpro.com')->update(['username' => 'manager']);
            DB::table('users')->whereNull('username')->update(['username' => DB::raw("SUBSTRING_INDEX(email, '@', 1)")]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};

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
        // Fix NULL emails by setting unique placeholder values
        DB::table('users')->whereNull('email')->get()->each(function ($user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['email' => 'user' . $user->id . '@placeholder.local']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};

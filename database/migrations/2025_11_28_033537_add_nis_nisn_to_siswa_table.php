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
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis', 20)->nullable()->unique()->after('nama_siswa');
            $table->string('nisn', 10)->nullable()->unique()->after('nis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique(['nis']);
            $table->dropUnique(['nisn']);
            $table->dropColumn(['nis', 'nisn']);
        });
    }
};

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
        Schema::table('kelas', function (Blueprint $table) {
            // Check if guru column doesn't exist before adding
            if (!Schema::hasColumn('kelas', 'guru')) {
                $table->foreignId('guru')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete()
                    ->after('tahun_ajaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'guru')) {
                $table->dropForeign(['guru']);
                $table->dropColumn('guru');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('jam_masuk')->nullable()->after('status');
            $table->time('jam_pulang')->nullable()->after('jam_masuk');
            $table->decimal('denda', 15, 2)->default(0)->nullable()->after('jam_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'jam_pulang', 'denda']);
        });
    }
};

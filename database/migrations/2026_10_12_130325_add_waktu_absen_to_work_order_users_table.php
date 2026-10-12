<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_users', function (Blueprint $table) {
            $table->dateTime('waktu_absen')->nullable()->after('hadir');
        });
    }

    public function down(): void
    {
        Schema::table('work_order_users', function (Blueprint $table) {
            $table->dropColumn('waktu_absen');
        });
    }
};

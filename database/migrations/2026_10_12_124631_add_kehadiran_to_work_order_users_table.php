<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_users', function (Blueprint $table) {
            $table->boolean('hadir')->default(false)->after('peran');
            $table->decimal('upah_dibayar', 15, 2)->nullable()->after('hadir');
            $table->text('catatan_kehadiran')->nullable()->after('upah_dibayar');
        });
    }

    public function down(): void
    {
        Schema::table('work_order_users', function (Blueprint $table) {
            $table->dropColumn(['hadir', 'upah_dibayar', 'catatan_kehadiran']);
        });
    }
};

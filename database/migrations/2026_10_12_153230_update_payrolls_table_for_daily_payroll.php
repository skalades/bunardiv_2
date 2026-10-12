<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('tipe_periode')->default('harian')->after('id');
            $table->date('tanggal')->nullable()->after('tipe_periode');
            $table->tinyInteger('bulan')->nullable()->change();
            $table->smallInteger('tahun')->nullable()->change();
            $table->dropUnique(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['tipe_periode', 'tanggal']);
            $table->unique(['bulan', 'tahun']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe_karyawan', ['bulanan', 'harian']);
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_transport', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->decimal('upah_event', 15, 2)->default(0);
            $table->integer('jumlah_hari_hadir')->default(0);
            $table->integer('jumlah_event_hadir')->default(0);
            $table->decimal('potongan_kasbon', 15, 2)->default(0);
            $table->decimal('potongan_lain', 15, 2)->default(0);
            $table->decimal('total_bruto', 15, 2)->default(0);
            $table->decimal('total_bersih', 15, 2)->default(0);
            $table->enum('status', ['draft', 'paid'])->default('draft');
            $table->date('tanggal_dibayar')->nullable();
            $table->string('metode_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['payroll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};

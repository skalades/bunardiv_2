<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->date('tanggal');
            $table->string('kategori');
            $table->string('judul');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('metode_pembayaran')->default('Tunai / Cash');
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('bukti_transaksi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

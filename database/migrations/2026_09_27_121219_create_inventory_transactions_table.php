<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete(); // PIC yang bertanggung jawab
            $table->string('tipe'); // keluar, masuk, opname
            $table->integer('qty');
            $table->dateTime('tanggal');
            $table->string('kondisi')->nullable(); // baik, rusak, hilang (biasanya diisi saat 'masuk')
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};

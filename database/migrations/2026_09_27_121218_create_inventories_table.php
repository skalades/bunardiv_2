<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // e.g., Peralatan Makan, Dekorasi
            $table->string('nama');
            $table->string('satuan')->default('pcs');
            $table->integer('stok_aktual')->nullable(); // Boleh null jika belum pernah opname (PRD FR-5)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

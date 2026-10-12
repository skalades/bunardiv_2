<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nik')->unique()->nullable();
            $table->string('jabatan')->nullable();
            $table->enum('tipe', ['bulanan', 'harian'])->default('harian');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('upah_per_event', 15, 2)->nullable()->comment('Khusus harian: upah flat per event/SPK');
            $table->decimal('tunjangan_transport', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->string('no_hp')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};

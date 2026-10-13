<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_loan_id')->constrained()->cascadeOnDelete();
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal');
            $table->enum('sumber', ['manual', 'potong_gaji'])->default('manual');
            $table->foreignId('payroll_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_loan_payments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to optimize MySQL and query performance.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'orders_status_index');
            $table->index('tanggal', 'orders_tanggal_index');
            $table->index(['status', 'tanggal'], 'orders_status_tanggal_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_date', 'payments_payment_date_index');
            $table->index(['order_id', 'payment_date'], 'payments_order_date_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('jatuh_tempo', 'invoices_jatuh_tempo_index');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->index('tanggal', 'inv_trans_tanggal_index');
            $table->index('tipe', 'inv_trans_tipe_index');
            $table->index(['inventory_id', 'tipe'], 'inv_trans_item_tipe_index');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->index('tanggal_persiapan', 'wo_tanggal_persiapan_index');
            $table->index('status', 'wo_status_index');
        });

        Schema::table('work_order_users', function (Blueprint $table) {
            $table->index(['user_id', 'hadir'], 'wo_users_user_hadir_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('tanggal', 'attendances_tanggal_index');
            $table->index('status', 'attendances_status_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('tanggal', 'expenses_tanggal_index');
            $table->index('kategori', 'expenses_kategori_index');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->index('tipe_periode', 'payrolls_tipe_periode_index');
            $table->index('tanggal', 'payrolls_tanggal_index');
            $table->index('status', 'payrolls_status_index');
            $table->index(['tahun', 'bulan'], 'payrolls_tahun_bulan_index');
        });

        Schema::table('payroll_items', function (Blueprint $table) {
            $table->index('status', 'payroll_items_status_index');
        });

        Schema::table('employee_loans', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'employee_loans_user_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_tanggal_index');
            $table->dropIndex('orders_status_tanggal_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_payment_date_index');
            $table->dropIndex('payments_order_date_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_jatuh_tempo_index');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('inv_trans_tanggal_index');
            $table->dropIndex('inv_trans_tipe_index');
            $table->dropIndex('inv_trans_item_tipe_index');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex('wo_tanggal_persiapan_index');
            $table->dropIndex('wo_status_index');
        });

        Schema::table('work_order_users', function (Blueprint $table) {
            $table->dropIndex('wo_users_user_hadir_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_tanggal_index');
            $table->dropIndex('attendances_status_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_tanggal_index');
            $table->dropIndex('expenses_kategori_index');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropIndex('payrolls_tipe_periode_index');
            $table->dropIndex('payrolls_tanggal_index');
            $table->dropIndex('payrolls_status_index');
            $table->dropIndex('payrolls_tahun_bulan_index');
        });

        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropIndex('payroll_items_status_index');
        });

        Schema::table('employee_loans', function (Blueprint $table) {
            $table->dropIndex('employee_loans_user_status_index');
        });
    }
};

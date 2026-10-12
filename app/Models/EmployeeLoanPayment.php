<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLoanPayment extends Model
{
    protected $fillable = [
        'employee_loan_id', 'jumlah_bayar', 'tanggal',
        'sumber', 'payroll_item_id', 'dicatat_oleh', 'catatan',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(EmployeeLoan::class, 'employee_loan_id');
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /** Kurangi sisa_hutang pada loan setelah payment disimpan */
    protected static function booted(): void
    {
        static::created(function (self $payment) {
            $loan = $payment->loan;
            $loan->sisa_hutang = max(0, $loan->sisa_hutang - $payment->jumlah_bayar);
            if ($loan->sisa_hutang <= 0) {
                $loan->status = 'lunas';
            }
            $loan->save();
        });
    }
}

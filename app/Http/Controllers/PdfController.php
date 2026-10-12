<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'setting' => $setting
        ])->setPaper('a4', 'portrait');
        
        return $pdf->stream('invoice-'.$order->no_order.'.pdf');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['order.customer', 'invoice']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'setting' => $setting
        ]);
        
        return $pdf->stream('kwitansi-'.$payment->reference_number.'.pdf');
    }

    public function spk(\App\Models\WorkOrder $workOrder)
    {
        $workOrder->load(['order.customer', 'pic', 'users.user', 'equipment.inventory']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.spk', [
            'workOrder' => $workOrder,
            'setting' => $setting
        ]);
        
        return $pdf->stream('SPK-'.$workOrder->spk_number.'.pdf');
    }

    public function suratJalan(\App\Models\WorkOrder $workOrder)
    {
        $workOrder->load(['order.customer', 'pic', 'users.user', 'equipment.inventory']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.surat-jalan', [
            'workOrder' => $workOrder,
            'setting' => $setting
        ]);
        
        return $pdf->stream('SuratJalan-'.$workOrder->surat_jalan_number.'.pdf');
    }

    public function slipGaji(\App\Models\PayrollItem $payrollItem)
    {
        $payrollItem->load(['user.employeeProfile', 'payroll']);
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.slip-gaji', [
            'item'    => $payrollItem,
            'user'    => $payrollItem->user,
            'payroll' => $payrollItem->payroll,
            'setting' => $setting,
        ])->setPaper('a5', 'landscape');

        return $pdf->stream('Slip-Gaji-'.$payrollItem->user->name.'-'.$payrollItem->payroll->nama_bulan.'.pdf');
    }
}

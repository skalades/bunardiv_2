<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PdfController;

Route::get('/orders/{order}/invoice', [PdfController::class, 'invoice'])->name('invoice.download')->middleware(['auth']);
Route::get('/payments/{payment}/receipt', [PdfController::class, 'receipt'])->name('receipt.download')->middleware(['auth']);

Route::get('/work-orders/{workOrder}/spk', [PdfController::class, 'spk'])->name('spk.download')->middleware(['auth']);
Route::get('/work-orders/{workOrder}/surat-jalan', [PdfController::class, 'suratJalan'])->name('surat_jalan.download')->middleware(['auth']);


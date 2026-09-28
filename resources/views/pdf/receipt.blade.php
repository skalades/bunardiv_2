<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $payment->reference_number ?? '-' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; margin: 0; padding: 0; }
        .receipt-container { border: 2px solid #2980b9; padding: 30px; position: relative; }
        
        .header-table { width: 100%; border-bottom: 2px solid #2980b9; padding-bottom: 15px; margin-bottom: 25px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo { width: 30%; }
        .header-logo img { max-width: 160px; max-height: 80px; }
        .header-info { width: 70%; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #2980b9; margin-bottom: 5px; }
        .company-contact { font-size: 12px; color: #555; line-height: 1.4; }
        
        .doc-title-container { text-align: center; margin-bottom: 30px; }
        .doc-title { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #2980b9; letter-spacing: 2px; text-decoration: underline; }
        
        .row-meta { display: table; width: 100%; margin-bottom: 20px; font-size: 13px; }
        .col-meta-left { display: table-cell; width: 50%; }
        .col-meta-right { display: table-cell; width: 50%; text-align: right; }
        
        .content-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; line-height: 2; }
        .content-table td { vertical-align: top; padding: 5px 0; border: none; }
        .label-col { width: 25%; font-weight: bold; color: #2c3e50; font-size: 13px; }
        .colon-col { width: 3%; text-align: center; font-weight: bold; }
        .value-col { width: 72%; font-size: 14px; }
        
        .dotted-line { border-bottom: 1px dashed #7f8c8d; display: inline-block; width: 100%; font-style: italic; color: #2c3e50; padding-bottom: 2px; }
        
        .amount-box { background: #ecf0f1; border: 2px solid #2980b9; padding: 12px 20px; font-size: 22px; display: inline-block; font-weight: bold; color: #2c3e50; border-radius: 4px; letter-spacing: 1px; }
        
        .signature-section { width: 100%; display: table; margin-top: 40px; }
        .signature-col { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; }
        .signature-title { margin-bottom: 70px; font-weight: bold; font-size: 13px; color: #2c3e50; }
        .signature-line { border-bottom: 1px solid #333; margin-bottom: 5px; width: 200px; margin-left: auto; margin-right: auto; }
        .signature-name { font-size: 12px; color: #555; }
        
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.05; font-size: 120px; font-weight: bold; color: #2980b9; z-index: -1; text-transform: uppercase; text-align: center; line-height: 1; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="watermark">LUNAS</div>
        
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($setting && $setting->company_logo)
                        <img src="{{ public_path('storage/'.$setting->company_logo) }}" alt="Logo">
                    @endif
                </td>
                <td class="header-info">
                    <div class="company-name">{{ $setting ? $setting->company_name : 'NAMA PERUSAHAAN' }}</div>
                    <div class="company-contact">
                        {{ $setting ? $setting->company_address : 'Alamat Perusahaan' }}<br>
                        Telp: {{ $setting ? $setting->company_phone : '-' }} 
                        @if($setting && $setting->company_email) | Email: {{ $setting->company_email }} @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="doc-title-container">
            <div class="doc-title">KWITANSI PEMBAYARAN</div>
        </div>

        <div class="row-meta">
            <div class="col-meta-left">
                <strong>No. Kwitansi:</strong> {{ $payment->reference_number ?? '-' }}
            </div>
            <div class="col-meta-right">
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d F Y') }}
            </div>
        </div>

        <table class="content-table">
            <tr>
                <td class="label-col">Sudah Terima Dari</td>
                <td class="colon-col">:</td>
                <td class="value-col"><span class="dotted-line"><strong>{{ $payment->order->customer->nama ?? '-' }}</strong></span></td>
            </tr>
            <tr>
                <td class="label-col">Banyaknya Uang</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <span class="amount-box">Rp {{ number_format($payment->amount_paid ?? $payment->amount, 0, ',', '.') }}</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Untuk Pembayaran</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <span class="dotted-line">Pesanan <strong>{{ $payment->order->nama_acara }}</strong> (No. Order: {{ $payment->order->no_order }})</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Metode Pembayaran</td>
                <td class="colon-col">:</td>
                <td class="value-col"><span class="dotted-line">{{ ucfirst($payment->payment_method) }}</span></td>
            </tr>
            @if($payment->notes)
            <tr>
                <td class="label-col">Keterangan Tambahan</td>
                <td class="colon-col">:</td>
                <td class="value-col"><span class="dotted-line">{{ $payment->notes }}</span></td>
            </tr>
            @endif
        </table>

        <div class="signature-section">
            <div class="signature-col">
                <br><br><br>
                <div style="font-size: 11px; text-align: left; padding-left: 20px; color: #7f8c8d; font-style: italic;">
                    * Kwitansi ini sah jika pembayaran<br>
                    telah masuk ke rekening kami.<br>
                    * Disimpan sebagai bukti pembayaran.
                </div>
            </div>
            <div class="signature-col">
                <div class="signature-title">Penerima,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Bagian Keuangan / Kasir</div>
            </div>
        </div>
    </div>
</body>
</html>

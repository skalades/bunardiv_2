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
        .header-logo img { max-width: 160px; max-height: 80px; object-fit: contain; }
        .emblem-box { display: inline-block; background-color: #0f172a; color: #facc15; font-weight: 900; font-size: 18px; padding: 10px 18px; border-radius: 4px; border-bottom: 3px solid #b48529; text-align: center; }
        .header-info { width: 70%; text-align: right; }
        .company-name { font-size: 20px; font-weight: bold; color: #2980b9; margin-bottom: 4px; text-transform: uppercase; }
        .company-contact { font-size: 11px; color: #555; line-height: 1.4; }
        
        .doc-title-container { text-align: center; margin-bottom: 25px; }
        .doc-title { font-size: 22px; font-weight: bold; text-transform: uppercase; color: #2980b9; letter-spacing: 2px; text-decoration: underline; }
        
        .row-meta { display: table; width: 100%; margin-bottom: 20px; font-size: 13px; }
        .col-meta-left { display: table-cell; width: 50%; }
        .col-meta-right { display: table-cell; width: 50%; text-align: right; }
        
        .content-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; line-height: 2; }
        .content-table td { vertical-align: top; padding: 5px 0; border: none; }
        .label-col { width: 25%; font-weight: bold; color: #2c3e50; font-size: 13px; }
        .colon-col { width: 3%; text-align: center; font-weight: bold; }
        .value-col { width: 72%; font-size: 14px; }
        
        .dotted-line { border-bottom: 1px dashed #7f8c8d; display: inline-block; width: 100%; font-style: italic; color: #2c3e50; padding-bottom: 2px; }
        
        .amount-box { background: #ecf0f1; border: 2px solid #2980b9; padding: 10px 18px; font-size: 20px; display: inline-block; font-weight: bold; color: #2c3e50; border-radius: 4px; letter-spacing: 1px; }
        
        .bank-info-box { background: #f8fafc; border: 1px solid #cbd5e1; border-left: 3px solid #2980b9; border-radius: 3px; padding: 8px 12px; font-size: 11px; margin-bottom: 20px; color: #475569; }
        .bank-info-title { font-weight: bold; color: #0f172a; margin-bottom: 3px; text-transform: uppercase; font-size: 10px; }

        .signature-section { width: 100%; display: table; margin-top: 25px; }
        .signature-col { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; }
        .signature-title { margin-bottom: 60px; font-weight: bold; font-size: 12px; color: #2c3e50; }
        .signature-line { border-bottom: 1px solid #333; margin-bottom: 5px; width: 200px; margin-left: auto; margin-right: auto; }
        .signature-name { font-size: 12px; color: #333; font-weight: bold; }
        .signature-sub { font-size: 11px; color: #64748b; }
        
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.05; font-size: 120px; font-weight: bold; color: #2980b9; z-index: -1; text-transform: uppercase; text-align: center; line-height: 1; }
    </style>
</head>
<body>
    @php
        $setting = $setting ?? \App\Models\Setting::getSettings();
        $logoPath = $setting->getLogoPath();
        $companyName = $setting->company_name ?: 'IBU NARDI';
        $companyAddress = $setting->company_address ?: 'Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut';
        $companyPhone = $setting->company_phone ?: '0852-8844-2884';
        $companyEmail = $setting->company_email ?: 'operasional@n7bunardi.com';
        $bankAccounts = $setting->getFormattedBankAccounts();
    @endphp

    <div class="receipt-container">
        <div class="watermark">LUNAS</div>
        
        <!-- KOP SURAT RESMI -->
        @include('pdf.partials.kop', ['setting' => $setting, 'dividerColor' => '#2980b9'])

        <div class="doc-title-container">
            <div class="doc-title">KWITANSI PEMBAYARAN</div>
        </div>

        <div class="row-meta">
            <div class="col-meta-left">
                <strong>No. Kwitansi:</strong> {{ $payment->reference_number ?? '-' }}
            </div>
            <div class="col-meta-right">
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d F Y') }}
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
                    <span class="dotted-line">
                        @if($payment->invoice)
                            <strong>{{ $payment->invoice->judul_tagihan ?: 'Invoice' }} ({{ $payment->invoice->no_invoice }})</strong> &bull; 
                        @endif
                        Pesanan <strong>{{ $payment->order->nama_acara }}</strong> (No. Order: {{ $payment->order->no_order }})
                    </span>
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

        <!-- INFORMASI REKENING RESMI PERUSAHAAN -->
        @if(!empty($bankAccounts))
        <div class="bank-info-box">
            <div class="bank-info-title">Rekening Resmi Penerimaan Pembayaran ({{ $companyName }}):</div>
            @foreach($bankAccounts as $ba)
                <span>&bull; {{ $ba['bank_name'] }}: <b>{{ $ba['account_number'] }}</b> (a.n {{ $ba['account_name'] }})</span> &nbsp;
            @endforeach
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-col">
                <br><br>
                <div style="font-size: 11px; text-align: left; padding-left: 10px; color: #7f8c8d; font-style: italic;">
                    * Kwitansi ini sah jika pembayaran telah masuk ke rekening resmi.<br>
                    * Bukti sah diterbitkan secara elektronik oleh sistem ERP.
                </div>
            </div>
            <div class="signature-col">
                <div class="signature-title">Diterima &amp; Diverifikasi Oleh,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Bagian Keuangan &amp; Kasir</div>
                <div class="signature-sub">{{ $companyName }}</div>
            </div>
        </div>
    </div>
</body>
</html>

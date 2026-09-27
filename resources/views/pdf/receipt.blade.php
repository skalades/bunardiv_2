<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $payment->reference_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header img { max-width: 150px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .company-info { font-size: 12px; }
        .row { width: 100%; display: table; }
        .col { display: table-cell; width: 50%; }
        .mb { margin-bottom: 20px; }
        .box { border: 1px solid #333; padding: 15px; margin-bottom: 20px; }
        .bold { font-weight: bold; }
        .amount-box { background: #f4f4f4; border: 1px solid #333; padding: 10px; font-size: 18px; display: inline-block; font-weight: bold; }
        .signature { text-align: right; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        @if($setting && $setting->company_logo)
            <img src="{{ public_path('storage/'.$setting->company_logo) }}" alt="Logo">
        @endif
        <div class="title">KWITANSI / BUKTI PEMBAYARAN</div>
        <div class="company-info">
            {{ $setting ? $setting->company_name : 'Nama Perusahaan' }}<br>
            {{ $setting ? $setting->company_address : '' }}<br>
            Telp: {{ $setting ? $setting->company_phone : '' }} | Email: {{ $setting ? $setting->company_email : '' }}
        </div>
    </div>

    <div class="row mb">
        <div class="col">
            <strong>No. Referensi:</strong> {{ $payment->reference_number ?? '-' }}
        </div>
        <div class="col" style="text-align: right;">
            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
        </div>
    </div>

    <div class="box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 25%; padding-bottom: 10px;"><strong>Telah Terima Dari</strong></td>
                <td style="width: 5%;">:</td>
                <td>{{ $payment->order->customer->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;"><strong>Uang Sebesar</strong></td>
                <td>:</td>
                <td><span class="amount-box">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;"><strong>Untuk Pembayaran</strong></td>
                <td>:</td>
                <td>Pesanan {{ $payment->order->nama_acara }} (No. Order: {{ $payment->order->no_order }})</td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;"><strong>Metode Pembayaran</strong></td>
                <td>:</td>
                <td>{{ $payment->payment_method }}</td>
            </tr>
            @if($payment->notes)
            <tr>
                <td style="padding-bottom: 10px;"><strong>Catatan</strong></td>
                <td>:</td>
                <td>{{ $payment->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="signature">
        <p>Penerima,</p>
        <br><br><br>
        <p>___________________________</p>
    </div>
</body>
</html>

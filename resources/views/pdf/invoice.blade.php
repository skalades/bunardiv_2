<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->no_order }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #8e44ad; padding-bottom: 15px; margin-bottom: 25px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo { width: 30%; }
        .header-logo img { max-width: 160px; max-height: 80px; }
        .header-info { width: 70%; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #8e44ad; margin-bottom: 5px; }
        .company-contact { font-size: 12px; color: #555; line-height: 1.4; }
        
        .doc-title-container { text-align: center; margin-bottom: 25px; }
        .doc-title { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #8e44ad; letter-spacing: 2px; }
        .doc-number { font-size: 14px; color: #7f8c8d; margin-top: 5px; }
        
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { vertical-align: top; border: none; padding: 0; }
        .info-box { background-color: #fcfcfc; border: 1px solid #eee; border-radius: 4px; padding: 12px; height: 100%; }
        .info-box-title { font-size: 12px; font-weight: bold; color: #95a5a6; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        .info-row { margin-bottom: 5px; display: table; width: 100%; }
        .info-label { display: table-cell; width: 100px; font-weight: bold; font-size: 12px; color: #34495e; }
        .info-value { display: table-cell; font-size: 13px; }
        
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-lunas { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-belum { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-dp { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; }
        .data-table th { background-color: #8e44ad; color: #ffffff; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .data-table tbody tr:nth-child(even) { background-color: #fbf8fc; }
        
        .totals-table { width: 50%; float: right; border-collapse: collapse; margin-bottom: 30px; }
        .totals-table td { padding: 8px 10px; border: 1px solid #eee; }
        .totals-label { font-weight: bold; color: #34495e; text-align: right; }
        .totals-value { text-align: right; font-weight: bold; }
        .totals-grand { font-size: 16px; color: #8e44ad; background-color: #fbf8fc; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .signature-table { width: 100%; margin-top: 20px; clear: both; }
        .signature-table td { vertical-align: bottom; border: none; padding: 0 15px; }
        .signature-title { margin-bottom: 70px; font-weight: bold; font-size: 13px; color: #2c3e50; }
        .signature-line { border-bottom: 1px solid #333; margin-bottom: 5px; width: 200px; }
        .signature-name { font-size: 12px; color: #555; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
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
        <div class="doc-title">INVOICE</div>
        <div class="doc-number">No. Tagihan: {{ $order->no_order }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 48%; padding-right: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Tagihan Kepada</div>
                    <div class="info-row"><span class="info-label">Nama Klien</span><span class="info-value">: <strong>{{ $order->customer ? $order->customer->nama : '-' }}</strong></span></div>
                    <div class="info-row"><span class="info-label">Alamat</span><span class="info-value">: {{ $order->customer ? $order->customer->alamat : '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Telepon</span><span class="info-value">: {{ $order->customer ? $order->customer->no_telp : '-' }}</span></div>
                </div>
            </td>
            <td style="width: 4%;">&nbsp;</td>
            <td style="width: 48%; padding-left: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Detail Order</div>
                    <div class="info-row"><span class="info-label">Tgl Order</span><span class="info-value">: {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Tgl Acara</span><span class="info-value">: {{ \Carbon\Carbon::parse($order->tanggal)->format('d F Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Status Bayar</span><span class="info-value">: 
                        @php
                            $statusLabel = 'Belum Lunas';
                            $statusClass = 'status-belum';
                            if($order->status_pembayaran == 'lunas') { $statusLabel = 'Lunas'; $statusClass = 'status-lunas'; }
                            if($order->status_pembayaran == 'dp') { $statusLabel = 'DP / Sebagian'; $statusClass = 'status-dp'; }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </span></div>
                </div>
            </td>
        </tr>
    </table>
    
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="45%">Deskripsi / Nama Item</th>
                <th width="15%" class="text-right">Harga</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="25%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->product ? $item->product->nama : 'Item Khusus' }}</strong>
                    @if($item->catatan)
                        <br><span style="font-size: 11px; color: #7f8c8d;">Catatan: {{ $item->catatan }}</span>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <table class="totals-table">
            <tr>
                <td class="totals-label">Subtotal</td>
                <td class="totals-value">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
            </tr>
            @php
                $totalPaid = $order->payments->sum('amount_paid');
                $remaining = $order->total_harga - $totalPaid;
            @endphp
            @if($totalPaid > 0)
            <tr>
                <td class="totals-label">Telah Dibayar</td>
                <td class="totals-value" style="color: #27ae60;">(Rp {{ number_format($totalPaid, 0, ',', '.') }})</td>
            </tr>
            @endif
            <tr class="totals-grand">
                <td class="totals-label">Sisa Tagihan</td>
                <td class="totals-value">Rp {{ number_format($remaining > 0 ? $remaining : 0, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 70%;">
                <div style="font-size: 11px; color: #7f8c8d;">
                    <strong>Metode Pembayaran:</strong><br>
                    Pembayaran dapat dilakukan melalui transfer ke rekening berikut:<br>
                    Bank XYZ - 1234567890 a/n BUNARDI Catering<br>
                    <em>Mohon cantumkan Nomor Tagihan saat melakukan transfer.</em>
                </div>
            </td>
            <td style="width: 30%; text-align: center;">
                <div class="signature-title">Hormat Kami,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Bagian Keuangan</div>
            </td>
        </tr>
    </table>
</body>
</html>

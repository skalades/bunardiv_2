<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->no_order }}</title>
    <style>
        @page {
            margin: 25px 28px 25px 28px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.35;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        /* HEADER */
        .header-table {
            margin-bottom: 12px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-left {
            width: 58%;
        }
        .company-logo {
            max-height: 55px;
            max-width: 170px;
            margin-bottom: 4px;
        }
        .company-name {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .company-desc {
            font-size: 9px;
            font-weight: 600;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .company-details {
            font-size: 9px;
            color: #64748b;
            line-height: 1.35;
        }
        
        .header-right {
            width: 42%;
            text-align: right;
        }
        .inv-badge-title {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 3px;
        }
        .inv-number {
            font-size: 12px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: 700;
            border-radius: 3px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        /* INFO CARDS */
        .meta-table {
            margin-bottom: 12px;
        }
        .meta-cell {
            vertical-align: top;
            padding: 0;
            border: none;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #4f46e5;
            padding: 8px 10px;
        }
        .info-card-event {
            border-left-color: #0ea5e9;
        }
        .info-card-title {
            font-size: 9.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .card-inner-table td {
            padding: 1.5px 0;
            font-size: 10px;
            vertical-align: top;
            border: none;
        }
        .ci-label {
            width: 85px;
            color: #64748b;
            font-weight: 500;
        }
        .ci-colon {
            width: 10px;
            color: #64748b;
            text-align: center;
        }
        .ci-val {
            color: #0f172a;
        }

        /* CURRENT BILLING HIGHLIGHT BOX */
        .billing-focus {
            background-color: #fffbeb;
            border: 1.5px solid #f59e0b;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .billing-focus-title {
            font-size: 10px;
            font-weight: 800;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .billing-focus-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .billing-desc {
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
        }
        .billing-amount {
            font-size: 16px;
            font-weight: 900;
            color: #b45309;
            text-align: right;
        }

        /* DATA TABLE */
        .data-table {
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .data-table thead {
            display: table-header-group;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 8px;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .item-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 10.5px;
        }

        /* BOTTOM SECTION */
        .bottom-table {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .bottom-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .bottom-left {
            width: 53%;
            padding-right: 12px;
        }
        .bottom-right {
            width: 47%;
        }

        .box-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 7px 9px;
            margin-bottom: 6px;
        }
        .box-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .bank-item {
            font-size: 9.5px;
            color: #334155;
            line-height: 1.4;
        }
        .bank-no {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #0f172a;
            background: #ede9fe;
            padding: 0 4px;
            border-radius: 2px;
        }
        .terms-text {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.35;
        }

        /* MINI TABLE FOR PAYMENTS */
        .mini-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }
        .mini-table th {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .mini-table td {
            font-size: 8.5px;
            padding: 3px 5px;
            border: 1px solid #e2e8f0;
            color: #334155;
        }

        /* TOTALS TABLE */
        .totals-table {
            border-collapse: collapse;
            width: 100%;
        }
        .totals-table td {
            padding: 4px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .tot-label {
            text-align: right;
            font-weight: 600;
            color: #475569;
            background-color: #f8fafc;
            width: 45%;
        }
        .tot-val {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
            width: 55%;
        }
        .tot-main {
            background-color: #f1f5f9;
        }
        .tot-main .tot-label, .tot-main .tot-val {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
        }
        .tot-current-tagihan {
            background-color: #fffbeb;
            border: 1.5px solid #f59e0b;
        }
        .tot-current-tagihan .tot-label {
            color: #b45309;
            font-weight: 800;
            font-size: 11.5px;
            background-color: #fffbeb;
        }
        .tot-current-tagihan .tot-val {
            color: #b45309;
            font-weight: 900;
            font-size: 12px;
        }

        /* SIGNATURE & FOOTER */
        .sig-table {
            margin-top: 6px;
            page-break-inside: avoid;
            width: 100%;
        }
        .sig-table td {
            vertical-align: bottom;
            border: none;
            padding: 0;
        }
        .sig-box {
            text-align: center;
            width: 180px;
            float: right;
        }
        .sig-date {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .sig-title {
            font-size: 9.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 42px;
        }
        .sig-line {
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 3px;
        }
        .sig-signer {
            font-size: 9.5px;
            font-weight: 700;
            color: #0f172a;
        }
        .sig-sub {
            font-size: 8px;
            color: #64748b;
        }

        .footer-note {
            font-size: 8px;
            color: #94a3b8;
            font-style: italic;
            line-height: 1.35;
        }

        /* UTILS */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .text-muted { color: #64748b; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>
    @php
        $totalPaid = $order->payments ? $order->payments->sum('amount') : 0;
        $remainingTotal = $order->total - $totalPaid;

        // Company branding
        $companyName = $setting && $setting->company_name ? $setting->company_name : 'BUNARDI CATERING & EVENT ORGANIZER';
        $companyAddress = $setting && $setting->company_address ? $setting->company_address : 'Jl. Danau Sunter Utara Blok F20 No. 24-25, Jakarta Utara';
        $companyPhone = $setting && $setting->company_phone ? $setting->company_phone : '0812-9876-5432 / 021-6543210';
        $companyEmail = $setting && $setting->company_email ? $setting->company_email : 'billing@bunardicatering.com';
        $companyLogoPath = $setting && $setting->company_logo ? public_path('storage/' . $setting->company_logo) : null;

        // Bank accounts
        $bankAccounts = ($setting && !empty($setting->bank_accounts)) ? $setting->bank_accounts : [
            ['bank_name' => 'BCA', 'account_number' => '5420-9988-77', 'account_name' => 'BUNARDI CATERING PT'],
            ['bank_name' => 'Bank Mandiri', 'account_number' => '120-00-9988776-5', 'account_name' => 'BUNARDI CATERING PT']
        ];

        // Format dates
        $orderDate = \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y');
        $eventDate = $order->tanggal ? \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d F Y') : '-';
        $eventTime = $order->jam ? substr($order->jam, 0, 5) . ' WIB' : '-';
    @endphp

    <!-- DomPDF Canvas Page Numbers Script -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $x = $pdf->get_width() - 28 - $width;
            $y = $pdf->get_height() - 20;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.55, 0.6, 0.65));
        }
    </script>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="header-left">
                @if($companyLogoPath && file_exists($companyLogoPath))
                    <img src="{{ $companyLogoPath }}" alt="Logo" class="company-logo"><br>
                @endif
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-desc">Professional Catering & Event Management</div>
                <div class="company-details">
                    {{ $companyAddress }}<br>
                    Telp / WA: {{ $companyPhone }} | Email: {{ $companyEmail }}
                </div>
            </td>
            <td class="header-right">
                <div class="inv-badge-title">INVOICE</div>
                <div class="inv-number">No. {{ $order->no_order }}</div>
                <div><span class="badge badge-warning">TAGIHAN KHUSUS (DP)</span></div>
            </td>
        </tr>
    </table>

    <!-- META CARDS -->
    <table class="meta-table">
        <tr>
            <td class="meta-cell" style="width: 48.5%;">
                <div class="info-card">
                    <div class="info-card-title">Tagihan Kepada (Client)</div>
                    <table class="card-inner-table">
                        <tr>
                            <td class="ci-label">Nama Klien</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val"><strong>{{ $order->customer->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="ci-label">Telepon / WA</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val">{{ $order->customer->no_telp ?? ($order->customer->no_wa ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="ci-label">Alamat</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val">{{ $order->customer->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ci-label">Tgl. Invoice</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val">{{ $orderDate }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 3%; border: none;"></td>
            <td class="meta-cell" style="width: 48.5%;">
                <div class="info-card info-card-event">
                    <div class="info-card-title">Detail Acara & Order (Event)</div>
                    <table class="card-inner-table">
                        <tr>
                            <td class="ci-label">Nama Acara</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val"><strong>{{ $order->nama_acara ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="ci-label">Jenis Acara</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val">{{ $order->jenis_acara ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ci-label">Waktu Acara</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val">{{ $eventDate }} ({{ $eventTime }})</td>
                        </tr>
                        <tr>
                            <td class="ci-label">Venue / Lokasi</td>
                            <td class="ci-colon">:</td>
                            <td class="ci-val">{{ $order->venue ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- HIGHLIGHT BOX: TAGIHAN SAAT INI -->
    <div class="billing-focus">
        <div class="billing-focus-title">Tagihan Saat Ini:</div>
        <table class="billing-focus-table">
            <tr>
                <td class="billing-desc">{{ $judul_tagihan }}</td>
                <td class="billing-amount">Rp {{ number_format($nominal_tagihan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- DATA TABLE ITEMS -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="47%" class="text-left">Deskripsi Produk / Layanan</th>
                <th width="16%" class="text-right">Harga Satuan</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="22%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td class="text-center text-muted">{{ $index + 1 }}</td>
                <td>
                    <div class="item-name">
                        {{ $item->product ? $item->product->nama : 'Item Khusus' }}
                        @if($item->product && $item->product->kategori)
                            <span class="item-cat">{{ $item->product->kategori }}</span>
                        @endif
                    </div>
                </td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ $item->qty }}</td>
                <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- BOTTOM SECTION -->
    <table class="bottom-table">
        <tr>
            <td class="bottom-left">
                <div class="box-panel">
                    <div class="box-title">Instruksi Pembayaran (Transfer Bank):</div>
                    @foreach($bankAccounts as $bank)
                        <div class="bank-item">
                            <strong>{{ $bank['bank_name'] }}</strong>: <span class="bank-no">{{ $bank['account_number'] }}</span> a.n {{ $bank['account_name'] }}
                        </div>
                    @endforeach
                    <div style="font-size: 8px; color: #64748b; margin-top: 3px;">
                        *Mohon sertakan No. Order <strong>{{ $order->no_order }}</strong> saat transfer & konfirmasi bukti bayar.
                    </div>
                </div>

                <div class="box-panel">
                    <div class="box-title">Syarat & Ketentuan Pembayaran:</div>
                    <div class="terms-text">
                        @if($setting && $setting->invoice_notes)
                            {!! nl2br(e($setting->invoice_notes)) !!}
                        @else
                            1. Pembayaran dianggap sah jika dana telah efektif masuk ke rekening kami.<br>
                            2. Pelunasan sisa tagihan wajib diselesaikan maksimal H-3 sebelum pelaksanaan acara.<br>
                            3. Konfirmasi perubahan jadwal atau menu wajib disampaikan secara tertulis maksimal H-7.
                        @endif
                    </div>
                </div>

                @if($order->payments && $order->payments->count() > 0)
                <div class="box-panel">
                    <div class="box-title">Riwayat Pembayaran Sebelumnya:</div>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Tgl Bayar</th>
                                <th>Metode</th>
                                <th>No. Ref</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td class="text-right font-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </td>
            <td class="bottom-right">
                <table class="totals-table">
                    <tr>
                        <td class="tot-label">Subtotal Order</td>
                        <td class="tot-val">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->diskon > 0)
                    <tr>
                        <td class="tot-label">Diskon</td>
                        <td class="tot-val text-danger">- Rp {{ number_format($order->diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($order->biaya_tambahan > 0)
                    <tr>
                        <td class="tot-label">Biaya Tambahan</td>
                        <td class="tot-val">+ Rp {{ number_format($order->biaya_tambahan, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="tot-main">
                        <td class="tot-label">Total Keseluruhan</td>
                        <td class="tot-val">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="tot-current-tagihan">
                        <td class="tot-label">{{ $judul_tagihan }}</td>
                        <td class="tot-val">Rp {{ number_format($nominal_tagihan, 0, ',', '.') }}</td>
                    </tr>
                    @if($totalPaid > 0)
                    <tr>
                        <td class="tot-label">Telah Dibayar Sebelumnya</td>
                        <td class="tot-val text-success">- Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="tot-label">Sisa Keseluruhan</td>
                        <td class="tot-val">Rp {{ number_format($remainingTotal > 0 ? $remainingTotal : 0, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SIGNATURE SECTION -->
    <table class="sig-table">
        <tr>
            <td style="width: 58%;">
                <div class="footer-note">
                    Terima kasih telah mempercayakan kebutuhan acara Anda kepada <strong>{{ $companyName }}</strong>.<br>
                    Dokumen ini adalah bukti penagihan resmi yang sah dan diterbitkan secara digital oleh sistem.
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="sig-box">
                    <div class="sig-date">Jakarta, {{ $orderDate }}</div>
                    <div class="sig-title">Hormat Kami,</div>
                    <div class="sig-line"></div>
                    <div class="sig-signer">Bagian Keuangan & Penagihan</div>
                    <div class="sig-sub">{{ $companyName }}</div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>

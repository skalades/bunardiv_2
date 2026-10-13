<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $periodeLabel }}</title>
    <style>
        @page {
            margin: 20px 26px 16px 26px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.3;
            color: #1e293b;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* ── HEADER BRAND ─────────────────────────────────────────── */
        .header-wrap {
            margin-bottom: 8px;
            border-bottom: 2px solid #cfa24b;
            padding-bottom: 8px;
        }
        .header-wrap td {
            vertical-align: middle;
            border: none;
        }
        .brand-title {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .brand-tagline {
            font-size: 8px;
            font-weight: 700;
            color: #cfa24b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 1px;
            margin-bottom: 2px;
        }
        .brand-info {
            font-size: 7.5px;
            color: #64748b;
            line-height: 1.25;
        }

        .doc-meta-right {
            text-align: right;
        }
        .doc-title-main {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .period-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #cfa24b;
            font-size: 8px;
            font-weight: 800;
            padding: 2.5px 7px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        .doc-submeta {
            font-size: 7px;
            color: #64748b;
        }

        /* ── 4 KPI CARDS (BALANCED SINGLE ROW) ─────────────────────── */
        .kpi-table {
            margin-bottom: 8px;
        }
        .kpi-table td {
            width: 25%;
            padding: 0 3px;
            vertical-align: top;
            border: none;
        }
        .kpi-table td:first-child { padding-left: 0; }
        .kpi-table td:last-child { padding-right: 0; }

        .kpi-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 2.5px solid #cfa24b;
            padding: 6px 8px;
            border-radius: 3px;
        }
        .kpi-box.blue { border-top-color: #2563eb; }
        .kpi-box.red { border-top-color: #dc2626; }
        .kpi-box.green {
            border-top-color: #059669;
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }

        .kpi-label {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .kpi-num {
            font-size: 11.5px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .kpi-num.gold { color: #b48529; }
        .kpi-num.blue { color: #1d4ed8; }
        .kpi-num.red { color: #dc2626; }
        .kpi-num.green { color: #047857; }

        .kpi-desc {
            font-size: 6.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── RATIO STRIP (COMPACT) ────────────────────────────────── */
        .ratio-strip {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            margin-bottom: 8px;
            padding: 4px 6px;
        }
        .ratio-strip td {
            width: 33.33%;
            padding: 1px 6px;
            vertical-align: middle;
            border: none;
        }
        .r-title {
            font-size: 7px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }
        .r-val {
            font-size: 8.5px;
            font-weight: 800;
            color: #0f172a;
        }
        .bar-bg {
            background-color: #e2e8f0;
            height: 3px;
            border-radius: 2px;
            margin-top: 2px;
        }
        .bar-fill {
            height: 3px;
            background-color: #cfa24b;
            border-radius: 2px;
        }
        .bar-fill.green { background-color: #10b981; }
        .bar-fill.red { background-color: #ef4444; }

        /* ── SECTION HEADER ───────────────────────────────────────── */
        .sec-header {
            font-size: 8px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            background-color: #f1f5f9;
            border-left: 3px solid #cfa24b;
            padding: 3.5px 6px;
            margin-bottom: 4px;
            margin-top: 4px;
        }

        /* ── P&L TABLE ────────────────────────────────────────────── */
        .pl-table {
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .pl-table th {
            background-color: #0f172a;
            color: #f8fafc;
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 6px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .pl-table th.right { text-align: right; }
        .pl-table td {
            padding: 3.5px 6px;
            font-size: 8px;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        .pl-table td.right {
            text-align: right;
            font-weight: 700;
        }
        .pl-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .cat-band {
            background-color: #f1f5f9 !important;
            font-weight: 800;
            font-size: 7.5px;
            text-transform: uppercase;
        }
        .cat-band td {
            color: #0f172a;
            padding: 3.5px 6px;
        }

        .subtotal-row {
            background-color: #f8fafc !important;
            font-weight: 800;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        .subtotal-row td {
            color: #0f172a;
        }

        .total-exp-row {
            background-color: #fef2f2 !important;
            border-top: 1.5px solid #f87171;
            border-bottom: 1.5px solid #f87171;
        }
        .total-exp-row td {
            color: #b91c1c;
            font-size: 8px;
            font-weight: 800;
        }

        .net-profit-row {
            background-color: #ecfdf5 !important;
            border-top: 1.5px solid #059669;
            border-bottom: 1.5px solid #059669;
        }
        .net-profit-row td {
            color: #047857;
            font-size: 9.5px;
            font-weight: 900;
            padding: 5px 6px;
        }

        /* ── 12-MONTH MATRIX COMPACT TABLE ────────────────────────── */
        .matrix-table {
            margin-bottom: 6px;
            border-collapse: collapse;
        }
        .matrix-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7px;
            font-weight: 800;
            padding: 3px 4px;
            text-align: right;
            border: 1px solid #1e293b;
            text-transform: uppercase;
        }
        .matrix-table th:first-child { text-align: left; }
        .matrix-table td {
            font-size: 7px;
            padding: 2.5px 4px;
            border: 1px solid #e2e8f0;
            text-align: right;
            color: #334155;
        }
        .matrix-table td:first-child {
            text-align: left;
            font-weight: 700;
            color: #0f172a;
        }
        .matrix-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .matrix-table tr.active-month {
            background-color: #fef9c3 !important;
            font-weight: 800;
        }
        .matrix-table tr.active-month td {
            color: #854d0e;
        }

        /* ── NOTE BOX ─────────────────────────────────────────────── */
        .note-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 2.5px solid #cfa24b;
            padding: 4px 6px;
            margin-bottom: 8px;
            border-radius: 2px;
        }
        .note-title {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .note-desc {
            font-size: 6.8px;
            color: #475569;
            line-height: 1.25;
        }

        /* ── SIGNATURES BLOCK (COMPACT FIT TO PAGE 1) ──────────────── */
        .sig-table {
            margin-top: 4px;
            page-break-inside: avoid;
        }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            border: none;
            padding: 0 20px;
            text-align: center;
        }
        .sig-cap {
            font-size: 7.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 28px;
        }
        .sig-name {
            font-size: 8.5px;
            font-weight: 800;
            color: #0f172a;
            border-top: 1px solid #64748b;
            padding-top: 2px;
            display: inline-block;
            min-width: 140px;
        }
        .sig-title-sub {
            font-size: 7px;
            color: #64748b;
            margin-top: 1px;
        }

        /* ── FOOTER ───────────────────────────────────────────────── */
        .footer-line {
            margin-top: 6px;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
            font-size: 6.5px;
            color: #94a3b8;
            text-align: center;
            letter-spacing: 0.4px;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI PERUSAHAAN -->
    <table class="header-wrap">
        <tr>
            <td class="brand-left">
                <div class="brand-title">{{ $setting->company_name ?? 'N7 DECORATION & CATERING BU NARDI' }}</div>
                <div class="brand-tagline">Jasa Katering Prasmanan, Paket Pernikahan & Dekorasi Acara</div>
                <div class="brand-info">
                    {{ $setting->company_address ?? 'Basecamp Logistik & Dapur Utama, Jakarta Barat' }}<br>
                    Telepon/WhatsApp: {{ $setting->company_phone ?? '0812-8899-7700' }} &bull; Email: {{ $setting->company_email ?? 'operasional@n7bunardi.com' }}
                </div>
            </td>
            <td class="doc-meta-right">
                <div class="doc-title-main">Laporan Keuangan Eksekutif</div>
                <div class="period-badge">{{ strtoupper($periodeLabel) }}</div>
                <div class="doc-submeta">
                    Metode: <b>{{ strtoupper($viewMode) === 'KAS' ? 'Basis Kas (Real Cash Inflow)' : 'Basis Akrual (Kontrak GMV)' }}</b><br>
                    Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB &bull; Ref: FIN-{{ $year }}{{ $month !== 'all' ? sprintf('%02d', $month) : 'ANNUAL' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- 4 KPI CARDS -->
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-box">
                    <div class="kpi-label">{{ $viewMode === 'kas' ? 'Realisasi Kas Masuk' : 'Total Omzet Kontrak' }}</div>
                    <div class="kpi-num gold">Rp {{ number_format($viewMode === 'kas' ? $totalKasMasuk : $totalNilaiOrder, 0, ',', '.') }}</div>
                    <div class="kpi-desc">{{ $totalOrderCount }} pesanan terdaftar</div>
                </div>
            </td>
            <td>
                <div class="kpi-box blue">
                    <div class="kpi-label">Piutang Pelanggan</div>
                    <div class="kpi-num blue">Rp {{ number_format($piutangBerjalan, 0, ',', '.') }}</div>
                    <div class="kpi-desc">Tagihan belum tertagih</div>
                </div>
            </td>
            <td>
                <div class="kpi-box red">
                    <div class="kpi-label">Total Beban (Biaya + Gaji)</div>
                    <div class="kpi-num red">Rp {{ number_format($totalBeban, 0, ',', '.') }}</div>
                    <div class="kpi-desc">Ops: {{ number_format($totalBebanOperasional, 0, ',', '.') }} • Gaji: {{ number_format($totalBebanPayroll, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-box green">
                    <div class="kpi-label">Laba / (Rugi) Bersih</div>
                    <div class="kpi-num green">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                    <div class="kpi-desc">Margin Laba: <b>{{ $profitMargin }}%</b></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- INDIKATOR RASIO & EFISIENSI -->
    <table class="ratio-strip">
        <tr>
            <td>
                <div class="r-title">Rasio Penagihan Kas (Collection Rate)</div>
                <div class="r-val" style="color: #1d4ed8;">{{ $collectionRate }}% <span style="font-size: 6.5px; font-weight: normal; color: #64748b;">dari total kontrak</span></div>
                <div class="bar-bg"><div class="bar-fill" style="width: {{ min(100, $collectionRate) }}%;"></div></div>
            </td>
            <td>
                <div class="r-title">Margin Laba Bersih (Net Profit Margin)</div>
                <div class="r-val" style="color: #047857;">{{ $profitMargin }}% <span style="font-size: 6.5px; font-weight: normal; color: #64748b;">terhadap pendapatan</span></div>
                <div class="bar-bg"><div class="bar-fill green" style="width: {{ min(100, max(0, $profitMargin)) }}%;"></div></div>
            </td>
            <td>
                <div class="r-title">Rasio Beban Operasional (Cost Ratio)</div>
                <div class="r-val" style="color: #b91c1c;">{{ $expenseRatio }}% <span style="font-size: 6.5px; font-weight: normal; color: #64748b;">terhadap pendapatan</span></div>
                <div class="bar-bg"><div class="bar-fill red" style="width: {{ min(100, $expenseRatio) }}%;"></div></div>
            </td>
        </tr>
    </table>

    <!-- LAPORAN LABA RUGI OPERASIONAL (P&L TABLE) -->
    <div class="sec-header">Laporan Laba Rugi Operasional (P&L Income Statement)</div>
    <table class="pl-table">
        <thead>
            <tr>
                <th style="width: 70%;">Pos Finansial / Akun Akuntansi</th>
                <th class="right" style="width: 30%;">Jumlah (Rupiah)</th>
            </tr>
        </thead>
        <tbody>
            <!-- I. PENDAPATAN -->
            <tr class="cat-band">
                <td colspan="2">I. PENDAPATAN USAHA (REVENUE)</td>
            </tr>
            @if($viewMode === 'akrual')
                <tr>
                    <td style="padding-left: 14px;">Total Nilai Kontrak Pesanan Acara (Gross Order Value)</td>
                    <td class="right">Rp {{ number_format($totalNilaiOrder, 0, ',', '.') }}</td>
                </tr>
                <tr class="subtotal-row">
                    <td style="padding-left: 14px;"><b>Subtotal Pendapatan Usaha (Metode Akrual)</b></td>
                    <td class="right"><b>Rp {{ number_format($totalNilaiOrder, 0, ',', '.') }}</b></td>
                </tr>
            @else
                <tr>
                    <td style="padding-left: 14px;">Realisasi Pembayaran Kas Diterima (DP & Pelunasan Bank/Tunai)</td>
                    <td class="right">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 14px; color: #64748b; font-style: italic;">Catatan: Piutang Pelanggan Berjalan Belum Tertagih</td>
                    <td class="right" style="color: #64748b;"><i>Rp {{ number_format($piutangBerjalan, 0, ',', '.') }}</i></td>
                </tr>
                <tr class="subtotal-row">
                    <td style="padding-left: 14px;"><b>Subtotal Kas Masuk Diterima (Metode Kas)</b></td>
                    <td class="right"><b>Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</b></td>
                </tr>
            @endif

            <!-- II. BEBAN OPERASIONAL -->
            <tr class="cat-band">
                <td colspan="2">II. BEBAN OPERASIONAL (OPERATING EXPENSES)</td>
            </tr>
            @forelse($expenseBreakdown as $exp)
                @php
                    $persenCat = $totalBebanOperasional > 0 ? round(($exp->total / $totalBebanOperasional) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="padding-left: 14px;">
                        {{ $exp->kategori }}
                        <span style="font-size: 6.5px; color: #64748b; margin-left: 4px;">({{ $persenCat }}%)</span>
                    </td>
                    <td class="right">Rp {{ number_format($exp->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td style="padding-left: 14px; color: #94a3b8; font-style: italic;">Belum ada pengeluaran operasional tercatat pada periode ini</td>
                    <td class="right" style="color: #94a3b8;">Rp 0</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td style="padding-left: 14px;"><b>Subtotal Beban Operasional</b></td>
                <td class="right" style="color: #b91c1c;"><b>Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</b></td>
            </tr>

            <!-- III. BEBAN SDM -->
            <tr class="cat-band">
                <td colspan="2">III. BEBAN SUMBER DAYA MANUSIA (PAYROLL & CREW WAGES)</td>
            </tr>
            <tr>
                <td style="padding-left: 14px;">Gaji Staf Kantor Tetap & Upah Kru Lapangan (SPK Acara)</td>
                <td class="right">Rp {{ number_format($totalBebanPayroll, 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal-row">
                <td style="padding-left: 14px;"><b>Subtotal Beban SDM & Penggajian</b></td>
                <td class="right" style="color: #b91c1c;"><b>Rp {{ number_format($totalBebanPayroll, 0, ',', '.') }}</b></td>
            </tr>

            <!-- TOTAL BEBAN -->
            <tr class="total-exp-row">
                <td><b>TOTAL BEBAN PENGELUARAN (II + III)</b></td>
                <td class="right"><b>(Rp {{ number_format($totalBeban, 0, ',', '.') }})</b></td>
            </tr>

            <!-- IV. LABA BERSIH -->
            <tr class="net-profit-row">
                <td>
                    <b>IV. LABA / (RUGI) BERSIH OPERASIONAL</b>
                    <span style="font-size: 7px; font-weight: normal; color: #065f46; margin-left: 6px;">(Total Pendapatan &minus; Total Seluruh Beban)</span>
                </td>
                <td class="right">
                    <b>Rp {{ number_format($labaBersih, 0, ',', '.') }}</b>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- REKAPITULASI 12 BULAN (JIKA LAPORAN TAHUNAN) -->
    @if(!empty($monthlySummary))
        <div class="sec-header">Matriks Rekapitulasi Kinerja Bulanan (Tahun {{ $year }})</div>
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Bulan</th>
                    <th style="width: 17%;">Pendapatan</th>
                    <th style="width: 17%;">Beban Ops</th>
                    <th style="width: 17%;">Beban Gaji</th>
                    <th style="width: 17%;">Total Beban</th>
                    <th style="width: 17%;">Laba Bersih</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlySummary as $mRow)
                    @php
                        $isActive = ($mRow['pendapatan'] > 0 || $mRow['total_beban'] > 0);
                    @endphp
                    <tr class="{{ $isActive ? 'active-month' : '' }}">
                        <td>{{ $mRow['bulan'] }}</td>
                        <td>Rp {{ number_format($mRow['pendapatan'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($mRow['beban_ops'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($mRow['beban_gaji'], 0, ',', '.') }}</td>
                        <td style="color: #b91c1c;">Rp {{ number_format($mRow['total_beban'], 0, ',', '.') }}</td>
                        <td style="font-weight: 800; color: {{ $mRow['laba_bersih'] >= 0 ? '#047857' : '#b91c1c' }};">
                            Rp {{ number_format($mRow['laba_bersih'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- CATATAN EKSEKUTIF -->
    <div class="note-card">
        <div class="note-title">Catatan Pengendalian Arus Kas & Piutang:</div>
        <div class="note-desc">
            1. Saldo piutang pelanggan berjalan tercatat <b>Rp {{ number_format($piutangBerjalan, 0, ',', '.') }}</b>. Bagian penagihan diwajibkan mengirimkan invoice pelunasan H-3 sebelum pelaksanaan acara.<br>
            2. Seluruh transaksi kas masuk dan pengeluaran operasional telah dicocokkan dengan rekening koran bank dan buku kas operasional.
        </div>
    </div>

    <!-- PENGESAHAN DUA TANDA TANGAN (COMPACT FIT TO PAGE 1) -->
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-cap">Disiapkan & Diverifikasi:</div>
                <div class="sig-name">{{ auth()->user()->name ?? 'Bagian Keuangan' }}</div>
                <div class="sig-title-sub">Finance & Accounting Administration</div>
            </td>
            <td>
                <div class="sig-cap">Disetujui & Disahkan:</div>
                <div class="sig-name">{{ $setting->company_name ?? 'Ibu Nardi' }}</div>
                <div class="sig-title-sub">Managing Director / Owner N7 Decoration</div>
            </td>
        </tr>
    </table>

    <div class="footer-line">
        Diterbitkan secara elektronik oleh Sistem ERP BUNARDI V2 &bull; Dokumen sah dan rahasia untuk keperluan pelaporan internal manajemen.
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Keuangan - {{ $periodeLabel }}</title>
    <style>
        @page {
            margin: 28px 32px 34px 32px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.38;
            color: #334155;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .right  { text-align: right  !important; }
        .center { text-align: center !important; }
        .bold   { font-weight: 700; }

        /* ── PAGE BREAK ──────────────────────────────────────────────── */
        .page-break { page-break-before: always; }

        /* ── RUNNING HEADER (PAGE 2+) ────────────────────────────────── */
        .running-header {
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 5px;
            margin-bottom: 14px;
        }
        .running-header td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .rh-left-brand {
            font-size: 8.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .rh-left-sub {
            font-size: 7px;
            color: #92622a;
            font-weight: 700;
        }
        .rh-right-meta {
            text-align: right;
            font-size: 7.2px;
            color: #64748b;
        }

        /* ── KOP SURAT (PAGE 1) ──────────────────────────────────────── */
        .kop-table {
            margin-bottom: 10px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .kop-emblem-cell {
            width: 54px;
            padding-right: 10px;
        }
        .brand-emblem {
            background-color: #0f172a;
            color: #facc15;
            font-weight: 900;
            font-size: 15px;
            text-align: center;
            padding: 9px 0;
            border-radius: 4px;
            border-bottom: 3px solid #92622a;
            letter-spacing: 0.8px;
        }
        .company-logo-img {
            max-height: 52px;
            max-width: 140px;
            object-fit: contain;
        }
        .kop-brand-title {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .kop-tagline {
            font-size: 7.5px;
            font-weight: 700;
            color: #92622a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        .kop-info {
            font-size: 7px;
            color: #64748b;
            line-height: 1.4;
        }
        .kop-doc-meta {
            text-align: right;
            width: 44%;
        }
        .doc-badge-pill {
            font-size: 6.5px;
            font-weight: 800;
            color: #92622a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
            display: block;
        }
        .doc-title-main {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.15;
        }
        .period-badge-box {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.8px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 3px;
            letter-spacing: 0.4px;
            margin-top: 3px;
            margin-bottom: 3px;
        }
        .period-badge-box span { color: #facc15; }
        .doc-meta-sub {
            font-size: 7px;
            color: #64748b;
            line-height: 1.4;
        }

        /* ── SECTION HEADER BAR ──────────────────────────────────────── */
        .section-header {
            font-size: 8px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            background-color: #f8fafc;
            border-left: 3.5px solid #92622a;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 8px;
            border-radius: 0 3px 3px 0;
        }

        /* ── 6 KPI CARDS  (2 rows × 3 cols) ─────────────────────────── */
        .kpi-grid-table { margin-bottom: 8px; }
        .kpi-grid-table td {
            width: 33.33%;
            padding: 0 3.5px;
            vertical-align: top;
            border: none;
        }
        .kpi-grid-table td:first-child { padding-left: 0; }
        .kpi-grid-table td:last-child  { padding-right: 0; }

        /* Base card */
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #0f172a;
            padding: 7px 9px;
            border-radius: 3px;
        }
        /* Colour variants */
        .kpi-card.navy    { border-top-color: #1e3a8a; }
        .kpi-card.gold    { border-top-color: #92622a; background-color: #fdfaf5; border-color: #e8d5b7; }
        .kpi-card.rose    { border-top-color: #9f1239; }
        .kpi-card.emerald { border-top-color: #065f46; background-color: #f0fdf4; border-color: #bbf7d0; }
        .kpi-card.blue    { border-top-color: #1e3a8a; background-color: #eff6ff; border-color: #bfdbfe; }
        .kpi-card.teal    { border-top-color: #0d9488; background-color: #f0fdfa; border-color: #99f6e4; }

        /* WARNING override – piutang besar */
        .kpi-card.warning-red {
            border-top-color: #9f1239 !important;
            background-color: #fff1f2 !important;
            border-color: #fca5a5 !important;
        }

        .kpi-label {
            font-size: 6.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 12px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
            margin-bottom: 2px;
        }
        .kpi-value.navy    { color: #1e3a8a; }
        .kpi-value.gold    { color: #92622a; }
        .kpi-value.rose    { color: #9f1239; }
        .kpi-value.emerald { color: #065f46; }
        .kpi-value.blue    { color: #1e3a8a; }
        .kpi-value.teal    { color: #0d9488; }
        .kpi-value.red     { color: #9f1239; }

        .kpi-subtext {
            font-size: 6.5px;
            color: #64748b;
        }
        .kpi-warning-label {
            font-size: 6.2px;
            font-weight: 800;
            color: #9f1239;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 2px;
        }

        /* ── PERFORMANCE RATIO BAR (3 columns) ──────────────────────── */
        .ratio-bar-wrap {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        .ratio-bar-wrap table td {
            width: 33.33%;
            padding: 5px 10px;
            vertical-align: middle;
            border: none;
        }
        .ratio-bar-wrap table td:not(:last-child) {
            border-right: 1px solid #e2e8f0;
        }
        .ratio-label {
            font-size: 6.5px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 1px;
        }
        .ratio-val {
            font-size: 9.5px;
            font-weight: 900;
            color: #0f172a;
        }
        .ratio-sub {
            font-size: 6.2px;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        .progress-bg {
            background-color: #e2e8f0;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
        }
        .progress-fill {
            height: 4px;
            border-radius: 2px;
            background-color: #1e3a8a;
        }
        .progress-fill.emerald { background-color: #065f46; }
        .progress-fill.rose    { background-color: #9f1239; }
        .progress-fill.teal    { background-color: #0d9488; }
        .progress-fill.red     { background-color: #9f1239; }
        .progress-fill.amber   { background-color: #92622a; }

        /* ── P&L TABLE ───────────────────────────────────────────────── */
        .pl-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .pl-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 8px;
            text-align: left;
        }
        .pl-table th.right  { text-align: right; }
        .pl-table th.center { text-align: center; }
        .pl-table td {
            padding: 4px 8px;
            font-size: 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .pl-table td.right  { text-align: right; font-weight: 600; }
        .pl-table td.center { text-align: center; }

        /* Category heading row */
        .pl-cat-head td {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 4px 8px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }

        /* Subtotal row */
        .pl-subtotal-row td {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: 700;
            padding: 4px 8px;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Total expense row – slate, NOT red */
        .pl-total-exp-row td {
            background-color: #1e293b;
            color: #f1f5f9;
            font-size: 8.5px;
            font-weight: 800;
            padding: 5px 8px;
            border-top: 1.5px solid #0f172a;
        }

        /* Net profit row */
        .pl-net-profit-row td {
            background-color: #f0fdf4;
            color: #065f46;
            font-size: 9px;
            font-weight: 900;
            padding: 6px 8px;
            border-top: 2px solid #065f46;
        }
        /* Net profit row when NEGATIVE */
        .pl-net-loss-row td {
            background-color: #fff1f2;
            color: #9f1239;
            font-size: 9px;
            font-weight: 900;
            padding: 6px 8px;
            border-top: 2px solid #9f1239;
        }

        /* ── CASH FLOW SUMMARY BOX ───────────────────────────────────── */
        .cashflow-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 7px 10px;
            margin-top: 6px;
            margin-bottom: 6px;
        }
        .cf-title {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 5px;
            letter-spacing: 0.4px;
        }
        .cf-table td {
            font-size: 7.8px;
            padding: 2px 0;
            border: none;
        }

        /* ── PAGE FOOTNOTE ───────────────────────────────────────────── */
        .page-footnote {
            font-size: 6.8px;
            color: #94a3b8;
            font-style: italic;
            margin-top: 6px;
            text-align: right;
        }

        /* ── MATRIX TABLE (PAGE 2 ANNUAL) ────────────────────────────── */
        .matrix-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .matrix-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7px;
            font-weight: 800;
            padding: 4.5px 6px;
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .matrix-table th:first-child { text-align: left; }
        .matrix-table td {
            font-size: 7.5px;
            padding: 3.5px 6px;
            border-bottom: 1px solid #e2e8f0;
            text-align: right;
            color: #334155;
        }
        .matrix-table td:first-child {
            text-align: left;
            font-weight: 700;
            color: #0f172a;
        }
        .matrix-table tbody tr:nth-child(even) { background-color: #fafbfc; }
        .matrix-table tr.active-month { background-color: #f0fdf4 !important; }
        .matrix-table tr.active-month td:first-child { color: #065f46; font-weight: 800; }
        .matrix-table tr.quarter-row {
            background-color: #f1f5f9 !important;
            border-top: 1px solid #94a3b8;
            border-bottom: 1px solid #94a3b8;
        }
        .matrix-table tr.quarter-row td {
            color: #0f172a;
            font-weight: 800;
            padding: 3.5px 6px;
        }
        .matrix-table tr.grand-annual-row {
            background-color: #0f172a !important;
            border-top: 2px solid #92622a;
        }
        .matrix-table tr.grand-annual-row td {
            color: #ffffff;
            font-size: 8px;
            font-weight: 900;
            padding: 5px 6px;
        }

        /* ── LEDGER TABLE (PAGE 2 MONTHLY) ──────────────────────────── */
        .ledger-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .ledger-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7px;
            font-weight: 800;
            padding: 4.5px 6px;
            text-align: left;
            text-transform: uppercase;
        }
        .ledger-table th.right  { text-align: right; }
        .ledger-table th.center { text-align: center; }
        .ledger-table td {
            font-size: 7.5px;
            padding: 3.5px 6px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: top;
        }
        .ledger-table td.right  { text-align: right; font-weight: 600; }
        .ledger-table td.center { text-align: center; }
        .ledger-table tbody tr:nth-child(even) { background-color: #fafbfc; }
        .ledger-total-row td {
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 8px;
            font-weight: 800;
            padding: 5px 6px;
            border-top: 1.5px solid #0f172a;
        }

        /* ── CATEGORY DIST TABLE ─────────────────────────────────────── */
        .cat-dist-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .cat-dist-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7px;
            font-weight: 800;
            padding: 4px 6px;
            text-align: left;
            text-transform: uppercase;
        }
        .cat-dist-table th.right  { text-align: right; }
        .cat-dist-table th.center { text-align: center; }
        .cat-dist-table td {
            font-size: 7.5px;
            padding: 3.5px 6px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .cat-dist-table td.right  { text-align: right; font-weight: 600; }
        .cat-dist-table td.center { text-align: center; }
        .cat-dist-table tbody tr:nth-child(even) { background-color: #fafbfc; }
        .cat-dist-total-row td {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: 800;
            border-top: 1px solid #cbd5e1;
            padding: 4px 6px;
        }

        /* ── ORDERS TABLE (PAGE 3) ───────────────────────────────────── */
        .orders-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .orders-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7px;
            font-weight: 800;
            padding: 4.5px 6px;
            text-align: left;
            text-transform: uppercase;
        }
        .orders-table th.right  { text-align: right; }
        .orders-table th.center { text-align: center; }
        .orders-table td {
            font-size: 7.5px;
            padding: 3.5px 6px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .orders-table td.right  { text-align: right; font-weight: 600; }
        .orders-table td.center { text-align: center; }
        .orders-table tbody tr:nth-child(even) { background-color: #fafbfc; }
        .orders-total-row td {
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 8px;
            font-weight: 800;
            padding: 5px 6px;
            border-top: 1.5px solid #0f172a;
        }

        .status-pill {
            display: inline-block;
            font-size: 6px;
            font-weight: 800;
            padding: 1.5px 4.5px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .status-paid    { background-color: #dcfce7; color: #166534; border: 0.5px solid #86efac; }
        .status-partial { background-color: #fef3c7; color: #92400e; border: 0.5px solid #fde68a; }
        .status-unpaid  { background-color: #fee2e2; color: #991b1b; border: 0.5px solid #fca5a5; }

        /* ── MANAGEMENT NOTES ────────────────────────────────────────── */
        .mgmt-notes-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #92622a;
            padding: 7px 10px;
            margin-top: 6px;
            margin-bottom: 12px;
            border-radius: 2px;
        }
        .mgmt-notes-title {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: 0.4px;
        }
        .mgmt-notes-desc {
            font-size: 7px;
            color: #475569;
            line-height: 1.5;
        }

        /* ── APPROVAL TABLE ──────────────────────────────────────────── */
        .approval-table {
            margin-top: 10px;
            page-break-inside: avoid;
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
        }
        .approval-table td {
            width: 33.33%;
            vertical-align: top;
            border: none;
            padding: 0 10px;
            text-align: center;
        }
        .appr-city-date {
            font-size: 7px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .appr-role-caption {
            font-size: 7.2px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 32px;
        }
        .appr-name {
            font-size: 8px;
            font-weight: 800;
            color: #0f172a;
            border-top: 1px solid #475569;
            padding-top: 3px;
            display: inline-block;
            min-width: 140px;
        }
        .appr-title-sub {
            font-size: 6.8px;
            color: #64748b;
            margin-top: 1px;
        }
        .stamp-box {
            display: inline-block;
            border: 1.5px solid #065f46;
            color: #065f46;
            font-size: 6px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 2px 6px;
            border-radius: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ── MINI SECTION TITLE (page 2 sub-sections) ───────────────── */
        .sub-section-title {
            font-size: 7.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 10px;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <!-- DomPDF Canvas Running Footer & Page Numbering -->
    <script type="text/php">
        if (isset($pdf)) {
            $font     = $fontMetrics->getFont("Helvetica", "normal");
            $boldFont = $fontMetrics->getFont("Helvetica", "bold");
            $size     = 7;
            $color    = array(0.45, 0.50, 0.55);

            $text  = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $xRight  = $pdf->get_width() - 32 - $width;
            $yBottom = $pdf->get_height() - 20;
            $pdf->page_text($xRight, $yBottom, $text, $font, $size, $color);

            $footerLeft = "{{ $setting->company_name ?? 'BUNARDI ERP' }} • Laporan Keuangan Eksekutif • Ref: {{ $docRef }} • Dokumen Sah Internal";
            $pdf->page_text(32, $yBottom, $footerLeft, $font, $size, $color);

            $lineY = $pdf->get_height() - 25;
            $pdf->line(32, $lineY, $pdf->get_width() - 32, $lineY, array(0.85, 0.88, 0.92), 0.5);
        }
    </script>

    <!-- ================================================================ -->
    <!-- HALAMAN 1: RINGKASAN EKSEKUTIF & LAPORAN LABA RUGI               -->
    <!-- ================================================================ -->

    <!-- KOP RESMI PERUSAHAAN -->
    <table class="kop-table">
        <tr>
            <td class="kop-emblem-cell">
                @if(!empty($companyLogoPath))
                    <img src="{{ $companyLogoPath }}" alt="Logo" class="company-logo-img">
                @else
                    <div class="brand-emblem">N7</div>
                @endif
            </td>
            <td>
                <div class="kop-brand-title">{{ $setting->company_name ?? 'N7 DECORATION & CATERING BU NARDI' }}</div>
                <div class="kop-tagline">Jasa Katering Prasmanan, Paket Pernikahan &amp; Dekorasi Acara</div>
                <div class="kop-info">
                    {{ $setting->company_address ?? 'Basecamp Logistik & Dapur Utama, Jakarta Barat' }}<br>
                    Telp/WA: {{ $setting->company_phone ?? '0812-8899-7700' }} &bull; {{ $setting->company_email ?? 'operasional@n7bunardi.com' }}
                </div>
            </td>
            <td class="kop-doc-meta">
                <span class="doc-badge-pill">Executive Financial Report</span>
                <div class="doc-title-main">LAPORAN KEUANGAN</div>
                <div class="period-badge-box"><span>PERIODE:</span> {{ strtoupper($periodeLabel) }}</div>
                <div class="doc-meta-sub">
                    Basis Akuntansi: <b>{{ strtoupper($viewMode) === 'KAS' ? 'Basis Kas (Real Cash Inflow)' : 'Basis Akrual (Kontrak GMV)' }}</b><br>
                    Ref: <b>{{ $docRef }}</b> &bull; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION I: KPI SCORECARD -->
    <div class="section-header">I. Ringkasan Eksekutif &amp; Indikator Kinerja Finansial (KPI Scorecard)</div>

    @php
        /* Piutang warning flag */
        $piutangWarning = ($piutangBerjalan > $totalKasMasuk * 2) && $totalKasMasuk > 0;

        /* Collection rate color */
        $colRateColor = $collectionRate < 50 ? 'red' : ($collectionRate < 80 ? 'gold' : 'teal');
        $colRateFill  = $collectionRate < 50 ? 'rose' : ($collectionRate < 80 ? 'amber' : 'teal');

        /* Profit margin color */
        $marginColor = $profitMargin < 0 ? 'rose' : 'emerald';
    @endphp

    <!-- ROW 1 OF 2 -->
    <table class="kpi-grid-table">
        <tr>
            <!-- Card 1: Kas Masuk / Omzet -->
            <td>
                <div class="kpi-card navy">
                    <div class="kpi-label">{{ $viewMode === 'kas' ? 'Kas Masuk Diterima' : 'Total Omzet Kontrak' }}</div>
                    <div class="kpi-value navy">Rp {{ number_format($viewMode === 'kas' ? $totalKasMasuk : $totalNilaiOrder, 0, ',', '.') }}</div>
                    <div class="kpi-subtext">{{ $totalOrderCount }} pesanan acara &bull; periode {{ $periodeLabel }}</div>
                </div>
            </td>
            <!-- Card 2: Piutang Berjalan (with warning if large) -->
            <td>
                <div class="kpi-card {{ $piutangWarning ? 'warning-red' : 'gold' }}">
                    <div class="kpi-label">Piutang Pelanggan Berjalan</div>
                    <div class="kpi-value {{ $piutangWarning ? 'red' : 'gold' }}">Rp {{ number_format($piutangBerjalan, 0, ',', '.') }}</div>
                    <div class="kpi-subtext">Tagihan belum terlunasi pelanggan</div>
                    @if($piutangWarning)
                        <div class="kpi-warning-label">&#9888; PERHATIAN: Piutang Sangat Besar</div>
                    @endif
                </div>
            </td>
            <!-- Card 3: Total Beban -->
            <td>
                <div class="kpi-card rose">
                    <div class="kpi-label">Total Beban (Ops + Gaji)</div>
                    <div class="kpi-value rose">Rp {{ number_format($totalBeban, 0, ',', '.') }}</div>
                    <div class="kpi-subtext">Ops: Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }} &bull; SDM: Rp {{ number_format($totalBebanPayroll, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ROW 2 OF 2 -->
    <table class="kpi-grid-table" style="margin-bottom: 10px;">
        <tr>
            <!-- Card 4: Laba Bersih -->
            <td>
                <div class="kpi-card emerald">
                    <div class="kpi-label">Laba / (Rugi) Bersih</div>
                    <div class="kpi-value {{ $labaBersih >= 0 ? 'emerald' : 'rose' }}">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                    <div class="kpi-subtext">{{ $labaBersih >= 0 ? 'Surplus operasional periode ini' : 'Defisit operasional periode ini' }}</div>
                </div>
            </td>
            <!-- Card 5: Net Profit Margin % -->
            <td>
                <div class="kpi-card blue">
                    <div class="kpi-label">Net Profit Margin</div>
                    <div class="kpi-value {{ $profitMargin < 0 ? 'rose' : 'blue' }}">{{ $profitMargin }}%</div>
                    <div class="kpi-subtext">Laba Bersih / Pendapatan Usaha</div>
                </div>
            </td>
            <!-- Card 6: Collection Rate % -->
            <td>
                <div class="kpi-card {{ $collectionRate < 50 ? 'warning-red' : 'teal' }}">
                    <div class="kpi-label">Collection Rate</div>
                    <div class="kpi-value {{ $colRateColor }}">{{ $collectionRate }}%</div>
                    <div class="kpi-subtext">Kas Diterima / Total Nilai Kontrak</div>
                    @if($collectionRate < 50)
                        <div class="kpi-warning-label">&#9888; KRITIS: Penagihan Perlu Ditingkatkan</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- RASIO EFISIENSI (PROGRESS BAR) -->
    <div class="ratio-bar-wrap">
        <table>
            <tr>
                <td>
                    <div class="ratio-label">Collection Rate (Penagihan Kas)</div>
                    <div class="ratio-val" style="color: {{ $collectionRate < 50 ? '#9f1239' : ($collectionRate < 80 ? '#92622a' : '#0d9488') }};">{{ $collectionRate }}%</div>
                    <div class="ratio-sub">Kas Masuk / Total Kontrak</div>
                    <div class="progress-bg">
                        <div class="progress-fill {{ $colRateFill }}" style="width: {{ min(100, $collectionRate) }}%;"></div>
                    </div>
                </td>
                <td>
                    <div class="ratio-label">Net Profit Margin</div>
                    <div class="ratio-val" style="color: {{ $profitMargin < 0 ? '#9f1239' : '#065f46' }};">{{ $profitMargin }}%</div>
                    <div class="ratio-sub">Laba Bersih / Pendapatan</div>
                    <div class="progress-bg">
                        <div class="progress-fill {{ $profitMargin < 0 ? 'rose' : 'emerald' }}" style="width: {{ min(100, max(0, $profitMargin)) }}%;"></div>
                    </div>
                </td>
                <td>
                    <div class="ratio-label">Rasio Beban Usaha</div>
                    <div class="ratio-val" style="color: {{ $expenseRatio > 85 ? '#9f1239' : '#1e3a8a' }};">{{ $expenseRatio }}%</div>
                    <div class="ratio-sub">Total Beban / Pendapatan</div>
                    <div class="progress-bg">
                        <div class="progress-fill {{ $expenseRatio > 85 ? 'rose' : '' }}" style="width: {{ min(100, $expenseRatio) }}%;"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- SECTION II: LAPORAN LABA RUGI -->
    <div class="section-header">II. Laporan Laba Rugi Operasional (Statement of Profit &amp; Loss)</div>

    <table class="pl-table">
        <thead>
            <tr>
                <th style="width: 58%;">Pos Finansial / Akun Akuntansi</th>
                <th class="right" style="width: 28%;">Jumlah Nominal (Rp)</th>
                <th class="right" style="width: 14%;">% Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <!-- I: PENDAPATAN -->
            <tr class="pl-cat-head">
                <td colspan="3">I. PENDAPATAN USAHA (REVENUE)</td>
            </tr>
            @if($viewMode === 'akrual')
                <tr>
                    <td style="padding-left: 16px;">Total Nilai Kontrak Pesanan Acara (Gross Merchandise Value)</td>
                    <td class="right">Rp {{ number_format($totalNilaiOrder, 0, ',', '.') }}</td>
                    <td class="right" style="color: #64748b;">100.0%</td>
                </tr>
                <tr class="pl-subtotal-row">
                    <td style="padding-left: 16px;"><b>Subtotal Pendapatan (Metode Akrual)</b></td>
                    <td class="right"><b>Rp {{ number_format($totalNilaiOrder, 0, ',', '.') }}</b></td>
                    <td class="right"><b>100.0%</b></td>
                </tr>
            @else
                <tr>
                    <td style="padding-left: 16px;">Realisasi Pembayaran Kas Diterima (DP &amp; Pelunasan)</td>
                    <td class="right">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                    <td class="right" style="color: #64748b;">100.0%</td>
                </tr>
                <tr>
                    <td style="padding-left: 16px; color: #64748b; font-style: italic;">
                        Piutang Pelanggan Belum Tertagih (informasi)
                    </td>
                    <td class="right" style="color: #64748b;"><i>Rp {{ number_format($piutangBerjalan, 0, ',', '.') }}</i></td>
                    <td class="right" style="color: #94a3b8;">
                        @php
                            $piutangPct = $totalKasMasuk > 0 ? round(($piutangBerjalan / $totalKasMasuk) * 100, 1) : 0;
                        @endphp
                        <i>{{ $piutangPct }}%</i>
                    </td>
                </tr>
                <tr class="pl-subtotal-row">
                    <td style="padding-left: 16px;"><b>Subtotal Kas Masuk Diterima (Metode Kas)</b></td>
                    <td class="right"><b>Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</b></td>
                    <td class="right"><b>100.0%</b></td>
                </tr>
            @endif

            <!-- II: BEBAN OPERASIONAL -->
            <tr class="pl-cat-head">
                <td colspan="3">II. BEBAN OPERASIONAL USAHA (OPERATING EXPENSES)</td>
            </tr>
            @php $pendapatan = $viewMode === 'akrual' ? $totalNilaiOrder : $totalKasMasuk; @endphp
            @forelse($expenseBreakdown as $exp)
                @php
                    $pctPend = $pendapatan > 0 ? round(($exp->total / $pendapatan) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="padding-left: 16px;">{{ $exp->kategori }}</td>
                    <td class="right">Rp {{ number_format($exp->total, 0, ',', '.') }}</td>
                    <td class="right" style="color: #475569;">{{ $pctPend }}%</td>
                </tr>
            @empty
                <tr>
                    <td style="padding-left: 16px; color: #94a3b8; font-style: italic;">Belum ada beban operasional tercatat pada periode ini</td>
                    <td class="right" style="color: #94a3b8;">Rp 0</td>
                    <td class="right" style="color: #94a3b8;">0.0%</td>
                </tr>
            @endforelse
            @php $opsBebanPct = $pendapatan > 0 ? round(($totalBebanOperasional / $pendapatan) * 100, 1) : 0; @endphp
            <tr class="pl-subtotal-row">
                <td style="padding-left: 16px;"><b>Subtotal Beban Operasional</b></td>
                <td class="right" style="color: #9f1239;"><b>Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</b></td>
                <td class="right" style="color: #9f1239;"><b>{{ $opsBebanPct }}%</b></td>
            </tr>

            <!-- III: BEBAN SDM -->
            <tr class="pl-cat-head">
                <td colspan="3">III. BEBAN SUMBER DAYA MANUSIA (PAYROLL &amp; FIELD CREW)</td>
            </tr>
            @php $payrollPct = $pendapatan > 0 ? round(($totalBebanPayroll / $pendapatan) * 100, 1) : 0; @endphp
            <tr>
                <td style="padding-left: 16px;">Gaji Staf Tetap, Upah Harian Kru Lapangan &amp; Lembur</td>
                <td class="right">Rp {{ number_format($totalBebanPayroll, 0, ',', '.') }}</td>
                <td class="right" style="color: #475569;">{{ $payrollPct }}%</td>
            </tr>
            <tr class="pl-subtotal-row">
                <td style="padding-left: 16px;"><b>Subtotal Beban SDM &amp; Penggajian</b></td>
                <td class="right" style="color: #9f1239;"><b>Rp {{ number_format($totalBebanPayroll, 0, ',', '.') }}</b></td>
                <td class="right" style="color: #9f1239;"><b>{{ $payrollPct }}%</b></td>
            </tr>

            <!-- TOTAL BEBAN -->
            @php $totalBebanPct = $pendapatan > 0 ? round(($totalBeban / $pendapatan) * 100, 1) : 0; @endphp
            <tr class="pl-total-exp-row">
                <td><b>TOTAL BEBAN PENGELUARAN USAHA (II + III)</b></td>
                <td class="right"><b>(Rp {{ number_format($totalBeban, 0, ',', '.') }})</b></td>
                <td class="right"><b>{{ $totalBebanPct }}%</b></td>
            </tr>

            <!-- LABA BERSIH -->
            @php $labaPct = $pendapatan > 0 ? round(($labaBersih / $pendapatan) * 100, 1) : 0; @endphp
            <tr class="{{ $labaBersih >= 0 ? 'pl-net-profit-row' : 'pl-net-loss-row' }}">
                <td>
                    <b>IV. LABA / (RUGI) BERSIH OPERASIONAL</b>
                    <span style="font-size: 7px; font-weight: normal; margin-left: 6px; opacity: 0.8;">(Total Pendapatan &minus; Total Seluruh Beban)</span>
                </td>
                <td class="right"><b>Rp {{ number_format($labaBersih, 0, ',', '.') }}</b></td>
                <td class="right"><b>{{ $labaPct }}%</b></td>
            </tr>
        </tbody>
    </table>

    <!-- REKONSILIASI ARUS KAS -->
    <div class="cashflow-box">
        <div class="cf-title">Rekonsiliasi Arus Kas Riil (Cash Flow Summary)</div>
        <table class="cf-table">
            <tr>
                <td style="width: 32%;">Kas Masuk dari Pembayaran Pelanggan</td>
                <td style="width: 2%;">:</td>
                <td class="bold" style="width: 22%; color: #065f46;">+ Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                <td style="width: 24%;">Total Kas Keluar (Ops + SDM)</td>
                <td style="width: 2%;">:</td>
                <td class="bold right" style="width: 18%; color: #9f1239;">&minus; Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><b>Net Arus Kas Periode Ini</b></td>
                <td>:</td>
                <td class="bold" style="color: {{ $netCashFlow >= 0 ? '#065f46' : '#9f1239' }}; font-size: 8.5px;">
                    Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                </td>
                <td>Status Arus Kas</td>
                <td>:</td>
                <td class="bold right" style="color: {{ $netCashFlow >= 0 ? '#065f46' : '#9f1239' }};">
                    {{ $netCashFlow >= 0 ? 'Surplus Kas Positif' : 'Defisit Kas Berjalan' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footnote">
        * Rincian analisis beban, matriks kinerja &amp; rekapitulasi pesanan disajikan pada Halaman 2 dan Halaman 3.
    </div>

    <!-- ================================================================ -->
    <!-- HALAMAN 2: ANALISIS BEBAN OPERASIONAL & KINERJA PERIODE          -->
    <!-- ================================================================ -->
    <div class="page-break"></div>

    <!-- RUNNING HEADER (PAGE 2) -->
    <table class="running-header">
        <tr>
            <td>
                <div class="rh-left-brand">{{ $setting->company_name ?? 'N7 DECORATION & CATERING BU NARDI' }}</div>
                <div class="rh-left-sub">Analisis Beban Operasional &amp; Matriks Kinerja Periode</div>
            </td>
            <td class="rh-right-meta">
                Periode: <b>{{ $periodeLabel }}</b> &bull; Ref: <b>{{ $docRef }}</b>
            </td>
        </tr>
    </table>

    <div class="section-header">Bagian II: Analisis Beban Operasional &amp; Kinerja Periode</div>

    @if(!empty($monthlySummary))
        <!-- ============== PERIODE TAHUNAN: MATRIKS 12 BULAN ============== -->
        <div class="sub-section-title">Matriks Kinerja Bulanan &amp; Kuartalan (Tahun {{ $year }})</div>
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 17%;">Bulan ({{ $year }})</th>
                    <th style="width: 17%;">Pendapatan</th>
                    <th style="width: 16%;">Beban Operasional</th>
                    <th style="width: 15%;">Beban Gaji (SDM)</th>
                    <th style="width: 17%;">Total Beban</th>
                    <th style="width: 18%;">Laba / (Rugi)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totPend = 0; $totOps = 0; $totGaji = 0; $totBeb = 0; $totLab = 0;
                @endphp
                @foreach($monthlySummary as $mRow)
                    @php
                        $isActive = ($mRow['pendapatan'] > 0 || $mRow['total_beban'] > 0);
                        $totPend += $mRow['pendapatan'];
                        $totOps  += $mRow['beban_ops'];
                        $totGaji += $mRow['beban_gaji'];
                        $totBeb  += $mRow['total_beban'];
                        $totLab  += $mRow['laba_bersih'];
                    @endphp
                    <tr class="{{ $isActive ? 'active-month' : '' }}">
                        <td>{{ $mRow['bulan'] }}</td>
                        <td>{{ $mRow['pendapatan'] > 0 ? 'Rp ' . number_format($mRow['pendapatan'], 0, ',', '.') : '-' }}</td>
                        <td>{{ $mRow['beban_ops'] > 0 ? 'Rp ' . number_format($mRow['beban_ops'], 0, ',', '.') : '-' }}</td>
                        <td>{{ $mRow['beban_gaji'] > 0 ? 'Rp ' . number_format($mRow['beban_gaji'], 0, ',', '.') : '-' }}</td>
                        <td style="color: {{ $mRow['total_beban'] > 0 ? '#475569' : '#94a3b8' }};">
                            {{ $mRow['total_beban'] > 0 ? 'Rp ' . number_format($mRow['total_beban'], 0, ',', '.') : '-' }}
                        </td>
                        <td style="font-weight: 700; color: {{ !$isActive ? '#94a3b8' : ($mRow['laba_bersih'] >= 0 ? '#065f46' : '#9f1239') }};">
                            {{ $isActive ? 'Rp ' . number_format($mRow['laba_bersih'], 0, ',', '.') : '-' }}
                        </td>
                    </tr>

                    {{-- Subtotal per Kuartal --}}
                    @if($mRow['bulan_num'] == 3)
                        <tr class="quarter-row">
                            <td>SUBTOTAL KUARTAL I (Q1)</td>
                            <td>Rp {{ number_format($quarterlySummary['Q1']['pendapatan'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q1']['beban_ops'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q1']['beban_gaji'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q1']['total_beban'], 0, ',', '.') }}</td>
                            <td style="color: {{ $quarterlySummary['Q1']['laba_bersih'] >= 0 ? '#065f46' : '#9f1239' }}; font-weight: 900;">
                                Rp {{ number_format($quarterlySummary['Q1']['laba_bersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @elseif($mRow['bulan_num'] == 6)
                        <tr class="quarter-row">
                            <td>SUBTOTAL KUARTAL II (Q2)</td>
                            <td>Rp {{ number_format($quarterlySummary['Q2']['pendapatan'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q2']['beban_ops'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q2']['beban_gaji'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q2']['total_beban'], 0, ',', '.') }}</td>
                            <td style="color: {{ $quarterlySummary['Q2']['laba_bersih'] >= 0 ? '#065f46' : '#9f1239' }}; font-weight: 900;">
                                Rp {{ number_format($quarterlySummary['Q2']['laba_bersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @elseif($mRow['bulan_num'] == 9)
                        <tr class="quarter-row">
                            <td>SUBTOTAL KUARTAL III (Q3)</td>
                            <td>Rp {{ number_format($quarterlySummary['Q3']['pendapatan'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q3']['beban_ops'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q3']['beban_gaji'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q3']['total_beban'], 0, ',', '.') }}</td>
                            <td style="color: {{ $quarterlySummary['Q3']['laba_bersih'] >= 0 ? '#065f46' : '#9f1239' }}; font-weight: 900;">
                                Rp {{ number_format($quarterlySummary['Q3']['laba_bersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @elseif($mRow['bulan_num'] == 12)
                        <tr class="quarter-row">
                            <td>SUBTOTAL KUARTAL IV (Q4)</td>
                            <td>Rp {{ number_format($quarterlySummary['Q4']['pendapatan'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q4']['beban_ops'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q4']['beban_gaji'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($quarterlySummary['Q4']['total_beban'], 0, ',', '.') }}</td>
                            <td style="color: {{ $quarterlySummary['Q4']['laba_bersih'] >= 0 ? '#065f46' : '#9f1239' }}; font-weight: 900;">
                                Rp {{ number_format($quarterlySummary['Q4']['laba_bersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr class="grand-annual-row">
                    <td>TOTAL KINERJA TAHUNAN</td>
                    <td>Rp {{ number_format($totPend, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($totOps, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($totGaji, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($totBeb, 0, ',', '.') }}</td>
                    <td style="font-weight: 900; color: #facc15;">Rp {{ number_format($totLab, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- TABEL DISTRIBUSI KATEGORI BEBAN TAHUNAN -->
        <div class="sub-section-title">Struktur Distribusi Kategori Beban Operasional (Tahun {{ $year }})</div>
        <table class="cat-dist-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 44%;">Kategori Biaya Operasional</th>
                    <th class="center" style="width: 16%;">Transaksi</th>
                    <th class="right" style="width: 20%;">Total Nominal</th>
                    <th class="right" style="width: 12%;">Porsi</th>
                </tr>
            </thead>
            <tbody>
                @php $nCat = 1; @endphp
                @forelse($expenseBreakdown as $expRow)
                    @php
                        $pctOfTot = $totalBebanOperasional > 0 ? round(($expRow->total / $totalBebanOperasional) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="center">{{ $nCat++ }}</td>
                        <td><b>{{ $expRow->kategori }}</b></td>
                        <td class="center">{{ $expRow->count ?? '-' }} kali</td>
                        <td class="right">Rp {{ number_format($expRow->total, 0, ',', '.') }}</td>
                        <td class="right">{{ $pctOfTot }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center" style="color: #94a3b8; padding: 10px;">Belum ada kategori pengeluaran tercatat.</td>
                    </tr>
                @endforelse
                <tr class="cat-dist-total-row">
                    <td colspan="3"><b>TOTAL BIAYA OPERASIONAL</b></td>
                    <td class="right"><b>Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</b></td>
                    <td class="right"><b>100.0%</b></td>
                </tr>
            </tbody>
        </table>

    @else
        <!-- ============== PERIODE BULANAN: BUKU CATATAN TRANSAKSI ============== -->
        <div class="sub-section-title">Buku Catatan Transaksi Pengeluaran Operasional (Bulan {{ $periodeLabel }})</div>
        <table class="ledger-table">
            <thead>
                <tr>
                    <th class="center" style="width: 6%;">No</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 22%;">Kategori Beban</th>
                    <th style="width: 40%;">Uraian Belanja / Deskripsi</th>
                    <th class="right" style="width: 18%;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $sumAllExp = 0; $nExp = 1; @endphp
                @forelse($allExpenses as $expItem)
                    @php $sumAllExp += $expItem->nominal; @endphp
                    <tr>
                        <td class="center">{{ $nExp++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($expItem->tanggal)->translatedFormat('d M Y') }}</td>
                        <td><b>{{ $expItem->kategori }}</b></td>
                        <td>{{ $expItem->judul }}</td>
                        <td class="right">Rp {{ number_format($expItem->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center" style="color: #94a3b8; padding: 12px;">
                            Tidak ada data pengeluaran operasional pada bulan ini.
                        </td>
                    </tr>
                @endforelse
                <tr class="ledger-total-row">
                    <td colspan="4"><b>TOTAL PENGELUARAN OPERASIONAL BULAN {{ strtoupper($periodeLabel) }}</b></td>
                    <td class="right"><b>Rp {{ number_format($sumAllExp, 0, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>

        <!-- DISTRIBUSI KATEGORI BULANAN -->
        <div class="sub-section-title">Komposisi Pengeluaran per Kategori &mdash; Bulan {{ $periodeLabel }}</div>
        <table class="cat-dist-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 44%;">Kategori Beban</th>
                    <th class="center" style="width: 16%;">Transaksi</th>
                    <th class="right" style="width: 20%;">Total Nominal</th>
                    <th class="right" style="width: 12%;">Porsi</th>
                </tr>
            </thead>
            <tbody>
                @php $nCat = 1; @endphp
                @forelse($expenseBreakdown as $expRow)
                    @php
                        $pctOfTot = $totalBebanOperasional > 0 ? round(($expRow->total / $totalBebanOperasional) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="center">{{ $nCat++ }}</td>
                        <td><b>{{ $expRow->kategori }}</b></td>
                        <td class="center">{{ $expRow->count ?? '-' }} kali</td>
                        <td class="right">Rp {{ number_format($expRow->total, 0, ',', '.') }}</td>
                        <td class="right">{{ $pctOfTot }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center" style="color: #94a3b8; padding: 10px;">Belum ada kategori pengeluaran tercatat.</td>
                    </tr>
                @endforelse
                <tr class="cat-dist-total-row">
                    <td colspan="3"><b>TOTAL BIAYA OPERASIONAL BULAN INI</b></td>
                    <td class="right"><b>Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</b></td>
                    <td class="right"><b>100.0%</b></td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="page-footnote">
        * Rekapitulasi pesanan acara, catatan pengendalian &amp; lembar pengesahan resmi disajikan pada Halaman 3.
    </div>

    <!-- ================================================================ -->
    <!-- HALAMAN 3: REKAPITULASI PESANAN & LEMBAR PENGESAHAN              -->
    <!-- ================================================================ -->
    <div class="page-break"></div>

    <!-- RUNNING HEADER (PAGE 3) -->
    <table class="running-header">
        <tr>
            <td>
                <div class="rh-left-brand">{{ $setting->company_name ?? 'N7 DECORATION & CATERING BU NARDI' }}</div>
                <div class="rh-left-sub">Portofolio Pesanan Acara, Pengendalian Piutang &amp; Lembar Pengesahan</div>
            </td>
            <td class="rh-right-meta">
                Periode: <b>{{ $periodeLabel }}</b> &bull; Ref: <b>{{ $docRef }}</b>
            </td>
        </tr>
    </table>

    <div class="section-header">Bagian III: Rekapitulasi Portofolio Pesanan Acara &amp; Pengendalian Arus Kas</div>

    <!-- TABEL REKAPITULASI PESANAN ACARA -->
    <table class="orders-table">
        <thead>
            <tr>
                <th class="center" style="width: 5%;">No</th>
                <th style="width: 14%;">No. Pesanan</th>
                <th style="width: 26%;">Klien / Nama Acara</th>
                <th style="width: 12%;">Tgl Acara</th>
                <th class="right" style="width: 15%;">Nilai Kontrak</th>
                <th class="right" style="width: 14%;">Kas Diterima</th>
                <th class="right" style="width: 14%;">Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $nOrd = 1;
                $totContract = 0;
                $totPaid = 0;
                $totUnpaid = 0;
            @endphp
            @forelse($orders as $ord)
                @php
                    $paidAmount   = (float) $ord->payments->sum('amount');
                    $unpaidAmount = max(0, (float) $ord->total - $paidAmount);
                    $totContract += (float) $ord->total;
                    $totPaid     += $paidAmount;
                    $totUnpaid   += $unpaidAmount;
                    $clientName   = $ord->customer->nama ?? ($ord->nama_acara ?? 'Pelanggan');
                @endphp
                <tr>
                    <td class="center">{{ $nOrd++ }}</td>
                    <td><b>{{ $ord->no_order }}</b></td>
                    <td>
                        {{ $clientName }}
                        @if($ord->jenis_acara)
                            <span style="font-size: 6.5px; color: #64748b;">({{ $ord->jenis_acara }})</span>
                        @endif
                    </td>
                    <td>{{ $ord->tanggal ? \Carbon\Carbon::parse($ord->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                    <td class="right">Rp {{ number_format($ord->total, 0, ',', '.') }}</td>
                    <td class="right" style="color: #065f46;">Rp {{ number_format($paidAmount, 0, ',', '.') }}</td>
                    <td class="right" style="color: {{ $unpaidAmount > 0 ? '#9f1239' : '#64748b' }};">
                        {{ $unpaidAmount > 0 ? 'Rp ' . number_format($unpaidAmount, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center" style="color: #94a3b8; padding: 12px;">
                        Tidak ada transaksi pesanan acara terdaftar pada periode ini.
                    </td>
                </tr>
            @endforelse
            <tr class="orders-total-row">
                <td colspan="4"><b>TOTAL PORTOFOLIO KONTRAK ({{ $orders->count() }} PESANAN)</b></td>
                <td class="right"><b>Rp {{ number_format($totContract, 0, ',', '.') }}</b></td>
                <td class="right" style="color: #065f46;"><b>Rp {{ number_format($totPaid, 0, ',', '.') }}</b></td>
                <td class="right" style="color: #9f1239;"><b>Rp {{ number_format($totUnpaid, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    <!-- CATATAN MANAJEMEN & PENGENDALIAN ARUS KAS -->
    <div class="mgmt-notes-card">
        <div class="mgmt-notes-title">Catatan Pengendalian Arus Kas, Piutang &amp; Manajemen Risiko</div>
        <div class="mgmt-notes-desc">
            1. <b>Manajemen Piutang Pelanggan:</b> Saldo piutang berjalan yang belum terlunasi tercatat sebesar <b>Rp {{ number_format($piutangBerjalan, 0, ',', '.') }}</b>. Tim penagihan &amp; admin diwajibkan mengirimkan invoice penagihan resmi pelunasan H-7 dan H-3 sebelum pelaksanaan acara agar seluruh kas telah masuk sebelum loading logistik dilakukan.<br>
            2. <b>Rekonsiliasi Kas &amp; Rekening Bank Operasional:</b> Seluruh mutasi kas masuk dan keluar dicocokkan dengan buku kas harian serta rekening koran resmi perbankan:
            <span style="display: block; margin: 2px 0 2px 10px; font-size: 7px; color: #1e293b;">
                @foreach($setting->getFormattedBankAccounts() as $bank)
                    &bull; <b>Bank {{ $bank['bank_name'] ?? 'Bank' }}</b>: {{ $bank['account_number'] ?? '-' }} a.n. {{ $bank['account_name'] ?? ($setting->company_name ?? '-') }} &nbsp;&nbsp;&nbsp;
                @endforeach
            </span>
            3. <b>Evaluasi Efisiensi Beban:</b> Beban bahan baku catering dan operasional logistik dekorasi terus dipantau dengan batas efisiensi standar industri katering (maksimal 65% terhadap omzet) guna menjamin margin laba bersih perusahaan tetap sehat dan bertumbuh berkelanjutan.
        </div>
    </div>

    <!-- LEMBAR PENGESAHAN DOKUMEN TRIPARTIT -->
    <table class="approval-table">
        <tr>
            <td>
                <div class="appr-city-date">&nbsp;</div>
                <div class="appr-role-caption">Disiapkan &amp; Diverifikasi:</div>
                <div class="appr-name">{{ auth()->user()->name ?? 'Bagian Administrasi Keuangan' }}</div>
                <div class="appr-title-sub">Finance &amp; Accounting Officer</div>
                <div class="stamp-box">TERVERIFIKASI SAH</div>
            </td>
            <td>
                <div class="appr-city-date">&nbsp;</div>
                <div class="appr-role-caption">Diperiksa &amp; Ditinjau:</div>
                <div class="appr-name">Bpk. Rahmat Santoso</div>
                <div class="appr-title-sub">Operations &amp; Field Manager</div>
                <div class="stamp-box">AUDIT INTERNAL OK</div>
            </td>
            <td>
                <div class="appr-city-date">Jakarta, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="appr-role-caption">Disetujui &amp; Disahkan:</div>
                <div class="appr-name">{{ $setting->company_name ?? 'Ibu Nardi' }}</div>
                <div class="appr-title-sub">Managing Director / Owner N7 Decoration</div>
                <div class="stamp-box" style="border-color: #92622a; color: #92622a;">PIMPINAN USAHA</div>
            </td>
        </tr>
    </table>

</body>
</html>

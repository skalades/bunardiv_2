<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        /* ============================================================
           LAPORAN KEUANGAN — PREMIUM DARK THEME UI
           ============================================================ */
        .lk-container {
            font-family: 'Inter', sans-serif;
            color: #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ---- FILTER HEADER ---- */
        .lk-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: linear-gradient(145deg, #181a24 0%, #121319 100%);
            border: 1px solid #262938;
            border-radius: 14px;
            padding: 16px 24px;
            flex-wrap: wrap;
        }

        .lk-header-brand {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .lk-header-brand-title {
            font-size: 15px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: -0.3px;
        }

        .lk-header-brand-sub {
            font-size: 11px;
            color: #8b92a4;
        }

        .lk-header-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .lk-select {
            background-color: #0f1015;
            border: 1px solid #2b2f3e;
            color: #f1f5f9;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 500;
            outline: none;
            cursor: pointer;
            transition: border-color 0.15s;
            min-width: 110px;
        }

        .lk-select:focus { border-color: #cfa24b; }

        .lk-sep {
            width: 1px;
            height: 28px;
            background: #262938;
        }

        .lk-basis-toggle {
            display: flex;
            background-color: #0f1015;
            border: 1px solid #262938;
            border-radius: 8px;
            overflow: hidden;
            padding: 2px;
        }

        .lk-basis-btn {
            background: transparent;
            border: none;
            color: #8b92a4;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .lk-basis-btn.active {
            background-color: #cfa24b;
            color: #0c0d10;
            font-weight: 700;
        }

        .lk-btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #1a1c25;
            color: #8b92a4;
            border: 1px solid #2b2f3e;
            padding: 7px 13px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }

        .lk-btn-action:hover {
            background-color: #20232e;
            color: #f1f5f9;
            border-color: #3b4255;
        }

        .lk-btn-action.gold {
            background: rgba(207, 162, 75, 0.12);
            border-color: rgba(207, 162, 75, 0.4);
            color: #cfa24b;
        }

        .lk-btn-action.gold:hover {
            background: rgba(207, 162, 75, 0.2);
        }

        /* ---- KPI PRIMARY GRID (4 cards) ---- */
        .lk-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        @media (max-width: 1024px) { .lk-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .lk-kpi-grid { grid-template-columns: 1fr; } }

        .lk-kpi-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .lk-kpi-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
        }

        .lk-kpi-card.gold::after   { background: linear-gradient(90deg, #cfa24b, #e6bf70); }
        .lk-kpi-card.green::after  { background: linear-gradient(90deg, #22c55e, #4ade80); }
        .lk-kpi-card.red::after    { background: linear-gradient(90deg, #ef4444, #f87171); }
        .lk-kpi-card.blue::after   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .lk-kpi-card.orange::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

        .lk-kpi-card.warning-state {
            border-color: rgba(239, 68, 68, 0.4);
            background: linear-gradient(145deg, #1e1517, #15161d);
        }

        .lk-kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .lk-kpi-title {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.9px;
            color: #5e6475;
            line-height: 1.4;
        }

        .lk-kpi-icon-wrap {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .lk-kpi-icon-wrap.gold   { background: rgba(207,162,75,0.12); color: #cfa24b; }
        .lk-kpi-icon-wrap.green  { background: rgba(34,197,94,0.12);  color: #22c55e; }
        .lk-kpi-icon-wrap.red    { background: rgba(239,68,68,0.12);  color: #ef4444; }
        .lk-kpi-icon-wrap.blue   { background: rgba(59,130,246,0.12); color: #3b82f6; }
        .lk-kpi-icon-wrap.orange { background: rgba(245,158,11,0.12); color: #f59e0b; }

        .lk-kpi-value {
            font-size: 21px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            line-height: 1.2;
            color: #f1f5f9;
            letter-spacing: -0.5px;
        }

        .lk-kpi-value.gold   { color: #cfa24b; }
        .lk-kpi-value.green  { color: #22c55e; }
        .lk-kpi-value.red    { color: #ef4444; }
        .lk-kpi-value.blue   { color: #3b82f6; }
        .lk-kpi-value.orange { color: #f59e0b; }

        .lk-kpi-sub {
            font-size: 11px;
            color: #5e6475;
            line-height: 1.4;
        }

        .lk-warning-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.35);
            color: #ef4444;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* ---- SECONDARY METRIC BAR ---- */
        .lk-metric-bar {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 12px;
            padding: 16px 22px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }

        @media (max-width: 640px) { .lk-metric-bar { grid-template-columns: 1fr; } }

        .lk-metric-item {
            padding: 4px 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .lk-metric-item:first-child { padding-left: 0; }
        .lk-metric-item:last-child { padding-right: 0; }

        .lk-metric-item + .lk-metric-item {
            border-left: 1px solid #20232e;
        }

        @media (max-width: 640px) {
            .lk-metric-item { padding: 10px 0; border-left: none !important; border-top: 1px solid #20232e; }
            .lk-metric-item:first-child { border-top: none; }
        }

        .lk-metric-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #5e6475;
        }

        .lk-metric-value-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }

        .lk-metric-num {
            font-size: 18px;
            font-weight: 700;
            font-family: monospace;
        }

        .lk-metric-progress {
            width: 100%;
            height: 4px;
            background: #20232e;
            border-radius: 99px;
            overflow: hidden;
            margin-top: 2px;
        }

        .lk-metric-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.5s ease;
        }

        /* ---- CHARTS ---- */
        .lk-charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
        }

        @media (max-width: 1024px) { .lk-charts-grid { grid-template-columns: 1fr; } }

        .lk-chart-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 14px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .lk-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .lk-chart-header-left {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .lk-chart-title {
            font-size: 13px;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .lk-chart-title i { color: #cfa24b; font-size: 16px; }

        .lk-chart-subtitle {
            font-size: 11px;
            color: #5e6475;
            padding-left: 23px;
        }

        .lk-chart-badge {
            font-size: 10px;
            font-weight: 700;
            background: rgba(207, 162, 75, 0.1);
            color: #cfa24b;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .lk-chart-container {
            position: relative;
            width: 100%;
            height: 300px;
        }

        .lk-empty-chart {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #5e6475;
            font-size: 12px;
            gap: 8px;
        }

        .lk-empty-chart i { font-size: 32px; color: #2b2f3e; }

        /* ---- TAB NAV ---- */
        .lk-tabs-nav {
            display: flex;
            gap: 6px;
            border-bottom: 1px solid #20232e;
            padding-bottom: 0;
            overflow-x: auto;
        }

        .lk-tab-btn {
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            color: #5e6475;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
            margin-bottom: -1px;
        }

        .lk-tab-btn:hover { color: #8b92a4; }

        .lk-tab-btn.active {
            color: #cfa24b;
            border-bottom-color: #cfa24b;
        }

        .lk-tab-pane {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ---- TABLE CARDS ---- */
        .lk-table-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 14px;
            padding: 22px 24px;
        }

        .lk-table-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #20232e;
        }

        .lk-table-title {
            font-size: 14px;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lk-table-title i { color: #cfa24b; }

        .lk-table-sub {
            font-size: 11px;
            color: #5e6475;
            margin-top: 3px;
        }

        /* P&L Table */
        .lk-pl-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .lk-pl-table th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #5e6475;
            padding: 9px 14px;
            border-bottom: 1px solid #262938;
        }

        .lk-pl-table th.right { text-align: right; }

        .lk-pl-table td {
            padding: 9px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1;
        }

        .lk-pl-table td.right {
            text-align: right;
            font-family: monospace;
            font-weight: 600;
        }

        .lk-pl-row-header td {
            background: rgba(255,255,255,0.02);
            color: #cfa24b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .lk-pl-row-total td {
            background: rgba(207,162,75,0.04);
            color: #f1f5f9;
            font-weight: 700;
            border-top: 1px solid rgba(207,162,75,0.15);
            border-bottom: 1px solid rgba(207,162,75,0.15);
        }

        .lk-pl-row-net td {
            background: linear-gradient(90deg, rgba(34,197,94,0.08), rgba(207,162,75,0.06));
            border-top: 2px solid rgba(34,197,94,0.4);
            font-size: 14px;
            font-weight: 700;
            padding: 13px 14px;
        }

        .lk-pl-row-net.loss td {
            background: linear-gradient(90deg, rgba(239,68,68,0.08), rgba(207,162,75,0.04));
            border-top-color: rgba(239,68,68,0.4);
        }

        /* Data Table */
        .lk-data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .lk-data-table th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #5e6475;
            padding: 10px 14px;
            border-bottom: 1px solid #262938;
            background: rgba(255,255,255,0.02);
        }

        .lk-data-table th.right  { text-align: right; }
        .lk-data-table th.center { text-align: center; }

        .lk-data-table td {
            padding: 9px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1;
        }

        .lk-data-table td.right  { text-align: right; font-family: monospace; font-weight: 600; }
        .lk-data-table td.center { text-align: center; }

        .lk-data-table tbody tr:hover { background: rgba(255,255,255,0.015); }

        .lk-table-total-row td {
            background: rgba(207,162,75,0.07);
            color: #f1f5f9;
            font-weight: 700;
            border-top: 1px solid rgba(207,162,75,0.25);
        }

        .lk-badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .lk-badge-green  { background: rgba(34,197,94,0.12);  color: #22c55e;  border: 1px solid rgba(34,197,94,0.25); }
        .lk-badge-yellow { background: rgba(245,158,11,0.12); color: #f59e0b;  border: 1px solid rgba(245,158,11,0.25); }
        .lk-badge-red    { background: rgba(239,68,68,0.12);  color: #ef4444;  border: 1px solid rgba(239,68,68,0.25); }

        @media print {
            .lk-header, .fi-sidebar, .fi-topbar, .lk-tabs-nav { display: none !important; }
            .lk-chart-card, .lk-table-card { box-shadow: none !important; border: 1px solid #ccc !important; background: #fff !important; color: #000 !important; break-inside: avoid; }
            .lk-tab-pane { display: flex !important; }
            .lk-print-break { page-break-before: always !important; }
        }
    </style>

    <div class="lk-container" x-data="laporanKeuanganApp()">

        {{-- =====================================================================
             FILTER / HEADER
             ===================================================================== --}}
        <div class="lk-header">
            {{-- Left: Branding --}}
            <div class="lk-header-brand">
                <div class="lk-header-brand-title">Laporan Keuangan</div>
                <div class="lk-header-brand-sub">
                    {{ $this->periodeLabel }} &bull; Basis <b style="color:#cfa24b;">{{ strtoupper($viewMode) }}</b>
                </div>
            </div>

            {{-- Right: Controls --}}
            <div class="lk-header-controls">
                <select wire:model.live="year" class="lk-select">
                    @foreach($this->availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>

                <select wire:model.live="month" class="lk-select">
                    @foreach($this->availableMonths as $val => $name)
                        <option value="{{ $val }}">{{ $name }}</option>
                    @endforeach
                </select>

                <div class="lk-sep"></div>

                <div class="lk-basis-toggle">
                    <button type="button" wire:click="setViewMode('kas')"
                            class="lk-basis-btn {{ $viewMode === 'kas' ? 'active' : '' }}">
                        Basis Kas
                    </button>
                    <button type="button" wire:click="setViewMode('akrual')"
                            class="lk-basis-btn {{ $viewMode === 'akrual' ? 'active' : '' }}">
                        Akrual
                    </button>
                </div>

                <div class="lk-sep"></div>

                <a href="{{ route('laporan_keuangan.pdf', ['year' => $year, 'month' => $month, 'viewMode' => $viewMode]) }}"
                   target="_blank" class="lk-btn-action gold">
                    <i class="ph ph-file-pdf"></i> PDF
                </a>
                <button type="button" onclick="window.print()" class="lk-btn-action">
                    <i class="ph ph-printer"></i> Cetak
                </button>
            </div>
        </div>

        {{-- =====================================================================
             KPI PRIMARY CARDS (4 utama)
             ===================================================================== --}}
        @php
            $kpi = $this->kpiStats;
            $isWarningPiutang = $kpi['piutangBerjalan'] > $kpi['totalKasMasuk'];
            $piutangColor = $isWarningPiutang ? 'red' : ($kpi['piutangBerjalan'] > ($kpi['totalKasMasuk'] * 0.4) ? 'orange' : 'blue');
            $isProfit = $kpi['labaBersih'] >= 0;
        @endphp

        <div class="lk-kpi-grid">

            {{-- 1. Kas Masuk / Omzet --}}
            <div class="lk-kpi-card gold">
                <div class="lk-kpi-top">
                    <div class="lk-kpi-title">{{ $viewMode === 'kas' ? 'Kas Masuk Diterima' : 'Total Omzet Kontrak' }}</div>
                    <div class="lk-kpi-icon-wrap gold"><i class="ph ph-trend-up"></i></div>
                </div>
                <div class="lk-kpi-value gold">
                    Rp {{ number_format($viewMode === 'kas' ? $kpi['totalKasMasuk'] : $kpi['totalNilaiOrder'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    {{ $kpi['totalOrderCount'] }} pesanan &bull;
                    @if($viewMode === 'kas') Realisasi pembayaran @else Nilai kontrak @endif
                </div>
            </div>

            {{-- 2. Total Beban --}}
            <div class="lk-kpi-card red">
                <div class="lk-kpi-top">
                    <div class="lk-kpi-title">Total Beban</div>
                    <div class="lk-kpi-icon-wrap red"><i class="ph ph-arrow-circle-up-right"></i></div>
                </div>
                <div class="lk-kpi-value red">
                    Rp {{ number_format($kpi['totalBeban'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    Ops: Rp {{ number_format($kpi['totalBebanOperasional'], 0, ',', '.') }}
                    &bull; Gaji: Rp {{ number_format($kpi['totalBebanPayroll'], 0, ',', '.') }}
                </div>
            </div>

            {{-- 3. Laba Bersih --}}
            <div class="lk-kpi-card {{ $isProfit ? 'green' : 'red' }}">
                <div class="lk-kpi-top">
                    <div class="lk-kpi-title">Laba Bersih</div>
                    <div class="lk-kpi-icon-wrap {{ $isProfit ? 'green' : 'red' }}">
                        <i class="ph {{ $isProfit ? 'ph-coins' : 'ph-warning-circle' }}"></i>
                    </div>
                </div>
                <div class="lk-kpi-value {{ $isProfit ? 'green' : 'red' }}">
                    Rp {{ number_format($kpi['labaBersih'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    Margin {{ $kpi['profitMargin'] }}%
                    &bull; {{ $isProfit ? 'Surplus' : 'Defisit' }}
                </div>
            </div>

            {{-- 4. Piutang Pelanggan (warning state if piutang > kasMasuk) --}}
            <div class="lk-kpi-card {{ $piutangColor }} {{ $isWarningPiutang ? 'warning-state' : '' }}">
                <div class="lk-kpi-top">
                    <div class="lk-kpi-title">Piutang Pelanggan</div>
                    <div class="lk-kpi-icon-wrap {{ $piutangColor }}">
                        <i class="ph {{ $isWarningPiutang ? 'ph-warning' : 'ph-credit-card' }}"></i>
                    </div>
                </div>
                <div class="lk-kpi-value {{ $piutangColor }}">
                    Rp {{ number_format($kpi['piutangBerjalan'], 0, ',', '.') }}
                </div>
                @if($isWarningPiutang)
                    <div class="lk-warning-badge">
                        <i class="ph ph-warning" style="font-size:11px;"></i>
                        Piutang melebihi kas masuk!
                    </div>
                @else
                    <div class="lk-kpi-sub">Tagihan belum lunas berjalan</div>
                @endif
            </div>
        </div>

        {{-- =====================================================================
             SECONDARY METRIC BAR (Collection Rate, Profit Margin, Expense Ratio)
             ===================================================================== --}}
        @php
            $collRate  = min((float)$kpi['collectionRate'], 100);
            $profitMrg = max(0, min((float)$kpi['profitMargin'], 100));
            $expRatio  = min((float)$kpi['expenseRatio'], 100);
        @endphp
        <div class="lk-metric-bar">
            {{-- Collection Rate --}}
            <div class="lk-metric-item">
                <div class="lk-metric-label">Collection Rate</div>
                <div class="lk-metric-value-row">
                    <span class="lk-metric-num"
                          style="color: {{ $collRate >= 80 ? '#22c55e' : ($collRate >= 50 ? '#f59e0b' : '#ef4444') }};">
                        {{ number_format($collRate, 1) }}%
                    </span>
                    <span style="font-size:10px;color:#5e6475;">Bayar / Kontrak</span>
                </div>
                <div class="lk-metric-progress">
                    <div class="lk-metric-bar-fill"
                         style="width: {{ $collRate }}%;
                                background: {{ $collRate >= 80 ? '#22c55e' : ($collRate >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                </div>
            </div>

            {{-- Profit Margin --}}
            <div class="lk-metric-item">
                <div class="lk-metric-label">Profit Margin</div>
                <div class="lk-metric-value-row">
                    <span class="lk-metric-num"
                          style="color: {{ $profitMrg >= 20 ? '#22c55e' : ($profitMrg >= 10 ? '#f59e0b' : '#ef4444') }};">
                        {{ number_format($profitMrg, 1) }}%
                    </span>
                    <span style="font-size:10px;color:#5e6475;">Dari Pendapatan</span>
                </div>
                <div class="lk-metric-progress">
                    <div class="lk-metric-bar-fill"
                         style="width: {{ $profitMrg }}%;
                                background: {{ $profitMrg >= 20 ? '#22c55e' : ($profitMrg >= 10 ? '#f59e0b' : '#ef4444') }};"></div>
                </div>
            </div>

            {{-- Expense Ratio --}}
            <div class="lk-metric-item">
                <div class="lk-metric-label">Expense Ratio</div>
                <div class="lk-metric-value-row">
                    <span class="lk-metric-num"
                          style="color: {{ $expRatio <= 60 ? '#22c55e' : ($expRatio <= 85 ? '#f59e0b' : '#ef4444') }};">
                        {{ number_format($expRatio, 1) }}%
                    </span>
                    <span style="font-size:10px;color:#5e6475;">Beban / Pendapatan</span>
                </div>
                <div class="lk-metric-progress">
                    <div class="lk-metric-bar-fill"
                         style="width: {{ min($expRatio, 100) }}%;
                                background: {{ $expRatio <= 60 ? '#22c55e' : ($expRatio <= 85 ? '#f59e0b' : '#ef4444') }};"></div>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             TAB NAVIGATION
             ===================================================================== --}}
        <div class="lk-tabs-nav">
            <button type="button" @click="activeTab = 'pnl'"
                    :class="activeTab === 'pnl' ? 'active' : ''" class="lk-tab-btn">
                Laba Rugi &amp; Tren Kinerja
            </button>
            <button type="button" @click="activeTab = 'matrix'"
                    :class="activeTab === 'matrix' ? 'active' : ''" class="lk-tab-btn">
                {{ $month === 'all' ? 'Matriks 12 Bulan' : 'Rincian Pengeluaran' }}
            </button>
            <button type="button" @click="activeTab = 'orders'"
                    :class="activeTab === 'orders' ? 'active' : ''" class="lk-tab-btn">
                Portofolio Pesanan ({{ $this->orders->count() }})
            </button>
        </div>

        {{-- =====================================================================
             TAB 1: LABA RUGI & TREN KINERJA
             ===================================================================== --}}
        <div class="lk-tab-pane" x-show="activeTab === 'pnl'">

            {{-- Charts grid --}}
            <div class="lk-charts-grid">
                {{-- Chart 1: Bar + Line P&L --}}
                <div class="lk-chart-card">
                    <div class="lk-chart-header">
                        <div class="lk-chart-header-left">
                            <div class="lk-chart-title">
                                <i class="ph ph-chart-bar"></i>
                                Grafik Laba Rugi Bulanan ({{ $year }})
                            </div>
                            <div class="lk-chart-subtitle">Tren 12 bulan pendapatan vs pengeluaran &amp; laba bersih</div>
                        </div>
                        <span class="lk-chart-badge">Pemasukan vs Beban</span>
                    </div>
                    <div class="lk-chart-container">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Donut --}}
                <div class="lk-chart-card">
                    <div class="lk-chart-header">
                        <div class="lk-chart-header-left">
                            <div class="lk-chart-title">
                                <i class="ph ph-chart-donut"></i>
                                Komposisi Biaya
                            </div>
                            <div class="lk-chart-subtitle">Distribusi per kategori pengeluaran</div>
                        </div>
                        <span class="lk-chart-badge">Kategori</span>
                    </div>
                    <div class="lk-chart-container">
                        <canvas id="expenseDonutChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Chart 3: Cashflow full-width --}}
            <div class="lk-chart-card">
                <div class="lk-chart-header">
                    <div class="lk-chart-header-left">
                        <div class="lk-chart-title">
                            <i class="ph ph-chart-line-up"></i>
                            Tren Arus Kas Riil (Cash Flow)
                        </div>
                        <div class="lk-chart-subtitle">Realisasi pembayaran masuk vs beban keluar per bulan</div>
                    </div>
                    <span class="lk-chart-badge" style="color:#22c55e;background:rgba(34,197,94,0.1);">Kas Riil</span>
                </div>
                <div class="lk-chart-container">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>

            {{-- P&L Income Statement Table --}}
            @php $pnl = $this->incomeStatementRows; @endphp
            <div class="lk-table-card">
                <div class="lk-table-header">
                    <div>
                        <div class="lk-table-title">
                            <i class="ph ph-file-text"></i>
                            Laporan Laba Rugi (P&L Income Statement)
                        </div>
                        <div class="lk-table-sub">
                            Periode: <b style="color:#f1f5f9;">{{ $this->periodeLabel }}</b>
                            &bull; Metode: <b style="color:#cfa24b;">{{ strtoupper($viewMode) }}</b>
                        </div>
                    </div>
                    <div style="font-family:monospace;font-size:12px;color:#5e6475;">IDR (Rp)</div>
                </div>

                <table class="lk-pl-table">
                    <thead>
                        <tr>
                            <th style="width:60%;">Deskripsi Akun / Pos Finansial</th>
                            <th class="right" style="width:40%;">Jumlah (Rupiah)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- I. PENDAPATAN --}}
                        <tr class="lk-pl-row-header">
                            <td colspan="2"><i class="ph ph-arrow-circle-down-right"></i> I. PENDAPATAN USAHA</td>
                        </tr>
                        @if($viewMode === 'akrual')
                            <tr>
                                <td style="padding-left:28px;">Pendapatan Kontrak Pesanan Acara (Gross)</td>
                                <td class="right">Rp {{ number_format($pnl['pendapatan']['omzet_order'], 0, ',', '.') }}</td>
                            </tr>
                            <tr class="lk-pl-row-total">
                                <td style="padding-left:28px;"><b>Total Pendapatan Usaha (Akrual)</b></td>
                                <td class="right" style="color:#cfa24b;">Rp {{ number_format($pnl['pendapatan']['omzet_order'], 0, ',', '.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td style="padding-left:28px;">Realisasi Pembayaran Kas Diterima (DP &amp; Pelunasan)</td>
                                <td class="right">Rp {{ number_format($pnl['pendapatan']['kas_diterima'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left:28px;color:#5e6475;"><i>Catatan: Piutang Pelanggan Berjalan Belum Tertagih</i></td>
                                <td class="right" style="color:#5e6475;"><i>Rp {{ number_format($pnl['pendapatan']['piutang'], 0, ',', '.') }}</i></td>
                            </tr>
                            <tr class="lk-pl-row-total">
                                <td style="padding-left:28px;"><b>Total Kas Diterima (Basis Kas)</b></td>
                                <td class="right" style="color:#cfa24b;">Rp {{ number_format($pnl['pendapatan']['kas_diterima'], 0, ',', '.') }}</td>
                            </tr>
                        @endif

                        {{-- II. BEBAN OPERASIONAL --}}
                        <tr class="lk-pl-row-header">
                            <td colspan="2"><i class="ph ph-arrow-circle-up-right"></i> II. BEBAN OPERASIONAL (EXPENSES)</td>
                        </tr>
                        @forelse($pnl['beban_operasional'] as $kategori => $nominal)
                            <tr>
                                <td style="padding-left:28px;">{{ $kategori }}</td>
                                <td class="right">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td style="padding-left:28px;color:#5e6475;"><i>Belum ada pengeluaran operasional tercatat pada periode ini</i></td>
                                <td class="right" style="color:#5e6475;">Rp 0</td>
                            </tr>
                        @endforelse
                        <tr class="lk-pl-row-total">
                            <td style="padding-left:28px;"><b>Subtotal Beban Operasional</b></td>
                            <td class="right" style="color:#ef4444;">Rp {{ number_format($kpi['totalBebanOperasional'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- III. BEBAN SDM / PAYROLL --}}
                        <tr class="lk-pl-row-header">
                            <td colspan="2"><i class="ph ph-users-three"></i> III. BEBAN SUMBER DAYA MANUSIA (PAYROLL)</td>
                        </tr>
                        <tr>
                            <td style="padding-left:28px;">Beban Gaji Staf Kantor &amp; Upah Kru Lapangan</td>
                            <td class="right">Rp {{ number_format($kpi['totalBebanPayroll'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="lk-pl-row-total">
                            <td style="padding-left:28px;"><b>Subtotal Beban SDM &amp; Penggajian</b></td>
                            <td class="right" style="color:#ef4444;">Rp {{ number_format($kpi['totalBebanPayroll'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- TOTAL BEBAN --}}
                        <tr class="lk-pl-row-total" style="background:rgba(239,68,68,0.06);">
                            <td><b>TOTAL BEBAN PENGELUARAN (II + III)</b></td>
                            <td class="right" style="color:#ef4444;font-size:14px;">(Rp {{ number_format($kpi['totalBeban'], 0, ',', '.') }})</td>
                        </tr>

                        {{-- IV. LABA / RUGI BERSIH --}}
                        <tr class="lk-pl-row-net {{ !$isProfit ? 'loss' : '' }}">
                            <td>
                                <div><b>IV. LABA / (RUGI) BERSIH OPERASIONAL</b></div>
                                <div style="font-size:11px;font-weight:normal;color:#5e6475;margin-top:2px;">
                                    Total Pendapatan &minus; Total Seluruh Beban
                                </div>
                            </td>
                            <td class="right" style="color:{{ $isProfit ? '#22c55e' : '#ef4444' }};font-size:17px;">
                                Rp {{ number_format($kpi['labaBersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>{{-- end tab pnl --}}

        {{-- =====================================================================
             TAB 2: MATRIKS / RINCIAN BEBAN
             ===================================================================== --}}
        <div class="lk-tab-pane lk-print-break" x-show="activeTab === 'matrix'">
            @if($month === 'all')
                <div class="lk-table-card">
                    <div class="lk-table-header">
                        <div>
                            <div class="lk-table-title">
                                <i class="ph ph-calendar"></i>
                                Matriks Rekapitulasi Kinerja Bulanan ({{ $year }})
                            </div>
                            <div class="lk-table-sub">Komparasi pendapatan, beban operasional, upah SDM, dan laba per bulan</div>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="lk-data-table">
                            <thead>
                                <tr>
                                    <th style="width:18%;">Bulan</th>
                                    <th class="right" style="width:16%;">Pendapatan Usaha</th>
                                    <th class="right" style="width:16%;">Beban Operasional</th>
                                    <th class="right" style="width:16%;">Beban Gaji (SDM)</th>
                                    <th class="right" style="width:16%;">Total Beban</th>
                                    <th class="right" style="width:18%;">Laba / (Rugi) Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totPend = 0; $totOps = 0; $totGaji = 0; $totBeb = 0; $totLab = 0; @endphp
                                @foreach($this->monthlySummary as $m)
                                    @php
                                        $totPend += $m['pendapatan'];
                                        $totOps  += $m['beban_ops'];
                                        $totGaji += $m['beban_gaji'];
                                        $totBeb  += $m['total_beban'];
                                        $totLab  += $m['laba_bersih'];
                                    @endphp
                                    <tr>
                                        <td><b>{{ $m['bulan'] }}</b></td>
                                        <td class="right">{{ $m['pendapatan'] > 0 ? 'Rp ' . number_format($m['pendapatan'], 0, ',', '.') : '-' }}</td>
                                        <td class="right">{{ $m['beban_ops'] > 0 ? 'Rp ' . number_format($m['beban_ops'], 0, ',', '.') : '-' }}</td>
                                        <td class="right">{{ $m['beban_gaji'] > 0 ? 'Rp ' . number_format($m['beban_gaji'], 0, ',', '.') : '-' }}</td>
                                        <td class="right" style="color:{{ $m['total_beban'] > 0 ? '#ef4444' : '#5e6475' }};">
                                            {{ $m['total_beban'] > 0 ? 'Rp ' . number_format($m['total_beban'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="right" style="color:{{ $m['laba_bersih'] >= 0 ? '#22c55e' : '#ef4444' }};font-weight:700;">
                                            Rp {{ number_format($m['laba_bersih'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="lk-table-total-row">
                                    <td><b>TOTAL TAHUNAN</b></td>
                                    <td class="right"><b>Rp {{ number_format($totPend, 0, ',', '.') }}</b></td>
                                    <td class="right"><b>Rp {{ number_format($totOps, 0, ',', '.') }}</b></td>
                                    <td class="right"><b>Rp {{ number_format($totGaji, 0, ',', '.') }}</b></td>
                                    <td class="right" style="color:#ef4444;"><b>Rp {{ number_format($totBeb, 0, ',', '.') }}</b></td>
                                    <td class="right" style="color:{{ $totLab >= 0 ? '#22c55e' : '#ef4444' }};font-size:14px;">
                                        <b>Rp {{ number_format($totLab, 0, ',', '.') }}</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="lk-table-card">
                    <div class="lk-table-header">
                        <div>
                            <div class="lk-table-title">
                                <i class="ph ph-receipt"></i>
                                Rincian Beban Operasional — {{ $this->periodeLabel }}
                            </div>
                            <div class="lk-table-sub">Catatan pembukuan seluruh belanja operasional bulan berjalan</div>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="lk-data-table">
                            <thead>
                                <tr>
                                    <th class="center" style="width:5%;">No</th>
                                    <th style="width:14%;">Tanggal</th>
                                    <th style="width:20%;">Kategori</th>
                                    <th style="width:43%;">Uraian Keperluan / Belanja</th>
                                    <th class="right" style="width:18%;">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $nE = 1; $totE = 0; @endphp
                                @forelse($this->expensesList as $exp)
                                    @php $totE += $exp->nominal; @endphp
                                    <tr>
                                        <td class="center">{{ $nE++ }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exp->tanggal)->translatedFormat('d M Y') }}</td>
                                        <td><span class="lk-badge lk-badge-yellow">{{ $exp->kategori }}</span></td>
                                        <td>{{ $exp->judul }}</td>
                                        <td class="right">Rp {{ number_format($exp->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="center" style="color:#5e6475;padding:24px;">
                                            Belum ada transaksi beban operasional pada bulan ini.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="lk-table-total-row">
                                    <td colspan="4"><b>TOTAL PENGELUARAN OPERASIONAL BULAN INI</b></td>
                                    <td class="right" style="color:#ef4444;"><b>Rp {{ number_format($totE, 0, ',', '.') }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>{{-- end tab matrix --}}

        {{-- =====================================================================
             TAB 3: PORTOFOLIO PESANAN ACARA
             ===================================================================== --}}
        <div class="lk-tab-pane lk-print-break" x-show="activeTab === 'orders'">
            <div class="lk-table-card">
                <div class="lk-table-header">
                    <div>
                        <div class="lk-table-title">
                            <i class="ph ph-calendar-check"></i>
                            Portofolio Pesanan Acara — {{ $this->periodeLabel }}
                        </div>
                        <div class="lk-table-sub">
                            Daftar acara katering &amp; dekorasi, realisasi pembayaran, dan status piutang berjalan
                        </div>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="lk-data-table">
                        <thead>
                            <tr>
                                <th class="center" style="width:5%;">No</th>
                                <th style="width:14%;">No. Pesanan</th>
                                <th style="width:25%;">Klien / Nama Acara</th>
                                <th style="width:12%;">Tgl Acara</th>
                                <th class="right" style="width:15%;">Nilai Kontrak</th>
                                <th class="right" style="width:14%;">Kas Diterima</th>
                                <th class="right" style="width:15%;">Sisa Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $nO = 1; $sumK = 0; $sumP = 0; $sumU = 0; @endphp
                            @forelse($this->orders as $ord)
                                @php
                                    $cPaid   = (float) $ord->payments->sum('amount');
                                    $cUnpaid = max(0, (float) $ord->total - $cPaid);
                                    $sumK   += (float) $ord->total;
                                    $sumP   += $cPaid;
                                    $sumU   += $cUnpaid;
                                @endphp
                                <tr>
                                    <td class="center">{{ $nO++ }}</td>
                                    <td><b>{{ $ord->no_order }}</b></td>
                                    <td>
                                        <div>{{ $ord->customer->nama ?? ($ord->nama_acara ?? 'Pelanggan') }}</div>
                                        @if($ord->jenis_acara)
                                            <div style="font-size:11px;color:#5e6475;">{{ $ord->jenis_acara }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $ord->tanggal ? \Carbon\Carbon::parse($ord->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                                    <td class="right">Rp {{ number_format($ord->total, 0, ',', '.') }}</td>
                                    <td class="right" style="color:#22c55e;">Rp {{ number_format($cPaid, 0, ',', '.') }}</td>
                                    <td class="right" style="color:{{ $cUnpaid > 0 ? '#ef4444' : '#5e6475' }};">
                                        {{ $cUnpaid > 0 ? 'Rp ' . number_format($cUnpaid, 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="center" style="color:#5e6475;padding:24px;">
                                        Belum ada data pesanan acara pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="lk-table-total-row">
                                <td colspan="4"><b>TOTAL PORTOFOLIO ({{ $this->orders->count() }} ACARA)</b></td>
                                <td class="right"><b>Rp {{ number_format($sumK, 0, ',', '.') }}</b></td>
                                <td class="right" style="color:#22c55e;"><b>Rp {{ number_format($sumP, 0, ',', '.') }}</b></td>
                                <td class="right" style="color:#ef4444;"><b>Rp {{ number_format($sumU, 0, ',', '.') }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{-- end tab orders --}}

    </div>{{-- end lk-container --}}

    {{-- =====================================================================
         CHART.JS INITIALIZATION & LIVEWIRE REACTIVITY
         ===================================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initFinancialCharts();
        });

        document.addEventListener('livewire:navigated', function () {
            initFinancialCharts();
        });

        window.addEventListener('update-financial-charts', function () {
            setTimeout(initFinancialCharts, 100);
        });

        let profitChartInstance    = null;
        let expenseDonutInstance   = null;
        let cashflowChartInstance  = null;

        function initFinancialCharts() {
            const monthlyData    = @json($this->monthlyChartData);
            const expenseCatData = @json($this->expenseCategoryData);
            const viewMode       = @json($this->viewMode);

            const gridColor  = 'rgba(255,255,255,0.04)';
            const tickColor  = '#5e6475';
            const tickFont   = { family: 'Inter', size: 11 };
            const monoFont   = { family: 'Courier New', size: 10 };

            const fmtRp = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
            const fmtAx = (value) => {
                const av = Math.abs(value);
                if (av >= 1000000000) return (value / 1000000000).toFixed(1) + 'M';
                if (av >= 1000000)    return (value / 1000000).toFixed(0) + 'Jt';
                return value;
            };

            // ---- 1. Profit Chart (Bar + Line) ----
            const ctxProfit = document.getElementById('profitChart');
            if (ctxProfit) {
                if (profitChartInstance) profitChartInstance.destroy();

                const incomeData    = viewMode === 'kas' ? monthlyData.kasMasuk : monthlyData.nilaiOrder;
                const netProfitData = viewMode === 'kas' ? monthlyData.labaBersihKas : monthlyData.labaBersihAkrual;

                profitChartInstance = new Chart(ctxProfit, {
                    type: 'bar',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [
                            {
                                label: viewMode === 'kas' ? 'Kas Masuk Diterima' : 'Nilai Omzet Order',
                                data: incomeData,
                                backgroundColor: 'rgba(207,162,75,0.8)',
                                borderColor: '#cfa24b',
                                borderWidth: 1,
                                borderRadius: 4,
                                order: 2
                            },
                            {
                                label: 'Total Beban (Ops + Gaji)',
                                data: monthlyData.totalBeban,
                                backgroundColor: 'rgba(239,68,68,0.7)',
                                borderColor: '#ef4444',
                                borderWidth: 1,
                                borderRadius: 4,
                                order: 2
                            },
                            {
                                label: 'Laba Bersih',
                                data: netProfitData,
                                type: 'line',
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34,197,94,0.12)',
                                borderWidth: 2.5,
                                tension: 0.3,
                                fill: false,
                                pointRadius: 4,
                                pointBackgroundColor: '#22c55e',
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { labels: { color: '#8b92a4', font: tickFont } },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ctx.dataset.label + ': ' + fmtRp(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            x: { ticks: { color: tickColor, font: tickFont }, grid: { color: gridColor } },
                            y: { ticks: { color: tickColor, font: monoFont, callback: fmtAx }, grid: { color: gridColor } }
                        }
                    }
                });
            }

            // ---- 2. Expense Donut ----
            const ctxDonut = document.getElementById('expenseDonutChart');
            if (ctxDonut) {
                if (expenseDonutInstance) expenseDonutInstance.destroy();

                const hasData = expenseCatData.values && expenseCatData.values.length > 0 && expenseCatData.values.some(v => v > 0);

                if (!hasData) {
                    ctxDonut.parentElement.innerHTML = `
                        <div class="lk-empty-chart">
                            <i class="ph ph-receipt"></i>
                            <div>Belum ada data pengeluaran pada periode ini.</div>
                        </div>`;
                } else {
                    expenseDonutInstance = new Chart(ctxDonut, {
                        type: 'doughnut',
                        data: {
                            labels: expenseCatData.labels,
                            datasets: [{
                                data: expenseCatData.values,
                                backgroundColor: expenseCatData.colors,
                                borderColor: '#15161d',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: '#8b92a4', font: { family: 'Inter', size: 10 }, boxWidth: 12 }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => ' ' + ctx.label + ': ' + fmtRp(ctx.raw)
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
            }

            // ---- 3. Cashflow Line Chart ----
            const ctxCashflow = document.getElementById('cashflowChart');
            if (ctxCashflow) {
                if (cashflowChartInstance) cashflowChartInstance.destroy();

                cashflowChartInstance = new Chart(ctxCashflow, {
                    type: 'line',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [
                            {
                                label: 'Arus Kas Masuk (Cash In — Realisasi Bayar)',
                                data: monthlyData.kasMasuk,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34,197,94,0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#22c55e',
                                pointRadius: 3
                            },
                            {
                                label: 'Arus Kas Keluar (Cash Out — Biaya & Gaji)',
                                data: monthlyData.totalBeban,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239,68,68,0.06)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#ef4444',
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { labels: { color: '#8b92a4', font: tickFont } },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ctx.dataset.label + ': ' + fmtRp(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            x: { ticks: { color: tickColor, font: tickFont }, grid: { color: gridColor } },
                            y: { ticks: { color: tickColor, font: monoFont, callback: fmtAx }, grid: { color: gridColor } }
                        }
                    }
                });
            }
        }

        function laporanKeuanganApp() {
            return {
                activeTab: 'pnl',
                init() {
                    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                        succeed(({ snapshot, effect }) => {
                            setTimeout(initFinancialCharts, 50);
                        });
                    });
                }
            }
        }
    </script>

</x-filament-panels::page>

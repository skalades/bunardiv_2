<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        .lk-container {
            font-family: 'Inter', sans-serif;
            color: #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .lk-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }

        /* Filter Header */
        .lk-filter-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: linear-gradient(145deg, #181a24 0%, #121319 100%);
            border: 1px solid #262938;
            border-radius: 14px;
            padding: 18px 24px;
        }

        .lk-filter-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .lk-filter-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #cfa24b;
        }

        .lk-select {
            background-color: #0f1015;
            border: 1px solid #2b2f3e;
            color: #f1f5f9;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 500;
            outline: none;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .lk-select:focus {
            border-color: #cfa24b;
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
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .lk-basis-btn.active {
            background-color: #cfa24b;
            color: #0c0d10;
            font-weight: 700;
        }

        .lk-btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #20232e;
            color: #cbd5e1;
            border: 1px solid #2d3242;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .lk-btn-print:hover {
            background-color: #2b3040;
            color: #fff;
        }

        /* KPI Cards Grid */
        .lk-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .lk-kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .lk-kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        .lk-kpi-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 12px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .lk-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background-color: transparent;
        }

        .lk-kpi-card.gold::before { background-color: #cfa24b; }
        .lk-kpi-card.green::before { background-color: #2ecc71; }
        .lk-kpi-card.red::before { background-color: #e74c3c; }
        .lk-kpi-card.blue::before { background-color: #3498db; }

        .lk-kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lk-kpi-title {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #8b92a4;
        }

        .lk-kpi-icon {
            font-size: 20px;
            color: #8b92a4;
        }

        .lk-kpi-value {
            font-size: 22px;
            font-weight: 700;
            font-family: monospace;
            line-height: 1.2;
            color: #f1f5f9;
        }

        .lk-kpi-value.gold { color: #cfa24b; }
        .lk-kpi-value.green { color: #2ecc71; }
        .lk-kpi-value.red { color: #e74c3c; }
        .lk-kpi-value.blue { color: #3498db; }

        .lk-kpi-sub {
            font-size: 11px;
            color: #64748b;
        }

        /* Charts Section */
        .lk-charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .lk-charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .lk-chart-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 14px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .lk-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lk-chart-title {
            font-size: 14px;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lk-chart-title i {
            color: #cfa24b;
            font-size: 18px;
        }

        .lk-chart-badge {
            font-size: 10px;
            font-weight: 700;
            background: rgba(207, 162, 75, 0.12);
            color: #cfa24b;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .lk-chart-container {
            position: relative;
            width: 100%;
            height: 320px;
        }

        /* P&L Table */
        .lk-table-card {
            background-color: #15161d;
            border: 1px solid #20232e;
            border-radius: 14px;
            padding: 24px;
        }

        .lk-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #20232e;
        }

        .lk-table-title {
            font-size: 16px;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lk-pl-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .lk-pl-table th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #71788e;
            padding: 10px 14px;
            border-bottom: 1px solid #262938;
        }

        .lk-pl-table th.right { text-align: right; }

        .lk-pl-table td {
            padding: 10px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #cbd5e1;
        }

        .lk-pl-table td.right {
            text-align: right;
            font-family: monospace;
            font-weight: 600;
        }

        .lk-pl-row-header {
            background-color: rgba(255, 255, 255, 0.02);
            font-weight: 700;
            color: #cfa24b !important;
            letter-spacing: 0.5px;
        }

        .lk-pl-row-header td {
            color: #cfa24b !important;
            padding-top: 14px;
            padding-bottom: 14px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .lk-pl-row-total {
            background-color: rgba(207, 162, 75, 0.05);
            font-weight: 700;
            border-top: 1px solid rgba(207, 162, 75, 0.2);
            border-bottom: 1px solid rgba(207, 162, 75, 0.2);
        }

        .lk-pl-row-total td {
            color: #f1f5f9;
        }

        .lk-pl-row-net {
            background: linear-gradient(90deg, rgba(46, 204, 113, 0.1), rgba(207, 162, 75, 0.1));
            border-top: 2px solid #2ecc71;
            font-size: 15px;
            font-weight: 700;
        }

        .lk-pl-row-net td {
            color: #2ecc71;
            padding: 14px;
        }

        .lk-empty-chart {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 13px;
            gap: 8px;
        }

        .lk-empty-chart i {
            font-size: 36px;
            color: #334155;
        }

        @media print {
            .lk-filter-header, .fi-sidebar, .fi-topbar {
                display: none !important;
            }
            .lk-card, .lk-chart-card, .lk-table-card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
                background: #fff !important;
                color: #000 !important;
            }
        }
    </style>

    <div class="lk-container" x-data="laporanKeuanganApp()">

        <!-- Filter & Control Header -->
        <div class="lk-filter-header">
            <div class="lk-filter-left">
                <span class="lk-filter-label"><i class="ph ph-funnel"></i> PERIODE:</span>
                
                <!-- Tahun -->
                <select wire:model.live="year" class="lk-select">
                    @foreach($this->availableYears as $yr)
                        <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                    @endforeach
                </select>

                <!-- Bulan -->
                <select wire:model.live="month" class="lk-select">
                    @foreach($this->availableMonths as $val => $name)
                        <option value="{{ $val }}">{{ $name }}</option>
                    @endforeach
                </select>

                <!-- Basis Pengakuan -->
                <div class="lk-basis-toggle">
                    <button
                        type="button"
                        wire:click="setViewMode('kas')"
                        class="lk-basis-btn {{ $viewMode === 'kas' ? 'active' : '' }}"
                    >
                        Basis Kas (Real Cash)
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('akrual')"
                        class="lk-basis-btn {{ $viewMode === 'akrual' ? 'active' : '' }}"
                    >
                        Basis Akrual (Kontrak)
                    </button>
                </div>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <a
                    href="{{ route('laporan_keuangan.pdf', ['year' => $year, 'month' => $month, 'viewMode' => $viewMode]) }}"
                    target="_blank"
                    class="lk-btn-print"
                    style="background: linear-gradient(135deg, rgba(207, 162, 75, 0.2), rgba(207, 162, 75, 0.08)); border-color: rgba(207, 162, 75, 0.5); color: #cfa24b; text-decoration: none; font-weight: 700;"
                >
                    <i class="ph ph-file-pdf" style="font-size: 16px;"></i>
                    <span>Download PDF</span>
                </a>
                <button type="button" onclick="window.print()" class="lk-btn-print">
                    <i class="ph ph-printer"></i>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- KPI Metric Cards Grid -->
        @php $kpi = $this->kpiStats; @endphp
        <div class="lk-kpi-grid">
            <!-- 1. Total Pemasukan / Pendapatan -->
            <div class="lk-kpi-card gold">
                <div class="lk-kpi-top">
                    <span class="lk-kpi-title">{{ $viewMode === 'kas' ? 'Kas Masuk Diterima' : 'Total Omzet Kontrak' }}</span>
                    <i class="ph ph-trend-up lk-kpi-icon" style="color: #cfa24b;"></i>
                </div>
                <div class="lk-kpi-value gold">
                    Rp {{ number_format($viewMode === 'kas' ? $kpi['totalKasMasuk'] : $kpi['totalNilaiOrder'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    @if($viewMode === 'kas')
                        Realisasi pembayaran dari {{ $kpi['totalOrderCount'] }} pesanan
                    @else
                        Nilai kontrak total {{ $kpi['totalOrderCount'] }} pesanan
                    @endif
                </div>
            </div>

            <!-- 2. Piutang Pelanggan -->
            <div class="lk-kpi-card blue">
                <div class="lk-kpi-top">
                    <span class="lk-kpi-title">Piutang Pelanggan</span>
                    <i class="ph ph-credit-card lk-kpi-icon" style="color: #3498db;"></i>
                </div>
                <div class="lk-kpi-value blue">
                    Rp {{ number_format($kpi['piutangBerjalan'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    Tagihan berjalan belum lunas
                </div>
            </div>

            <!-- 3. Total Beban Pengeluaran -->
            <div class="lk-kpi-card red">
                <div class="lk-kpi-top">
                    <span class="lk-kpi-title">Total Beban (Biaya + Gaji)</span>
                    <i class="ph ph-wallet lk-kpi-icon" style="color: #e74c3c;"></i>
                </div>
                <div class="lk-kpi-value red">
                    Rp {{ number_format($kpi['totalBeban'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    Ops: Rp {{ number_format($kpi['totalBebanOperasional'], 0, ',', '.') }} • Gaji: Rp {{ number_format($kpi['totalBebanPayroll'], 0, ',', '.') }}
                </div>
            </div>

            <!-- 4. Laba / Rugi Bersih -->
            <div class="lk-kpi-card {{ $kpi['labaBersih'] >= 0 ? 'green' : 'red' }}">
                <div class="lk-kpi-top">
                    <span class="lk-kpi-title">Estimasi Laba Bersih</span>
                    <i class="ph {{ $kpi['labaBersih'] >= 0 ? 'ph-coins' : 'ph-warning' }} lk-kpi-icon" style="color: {{ $kpi['labaBersih'] >= 0 ? '#2ecc71' : '#e74c3c' }};"></i>
                </div>
                <div class="lk-kpi-value {{ $kpi['labaBersih'] >= 0 ? 'green' : 'red' }}">
                    Rp {{ number_format($kpi['labaBersih'], 0, ',', '.') }}
                </div>
                <div class="lk-kpi-sub">
                    Margin: <b>{{ $kpi['profitMargin'] }}%</b> dari pendapatan
                </div>
            </div>
        </div>

        <!-- Charts Section (Grid: 2 Kolom) -->
        <div class="lk-charts-grid">
            <!-- Chart 1: Laba Rugi Bulanan -->
            <div class="lk-chart-card">
                <div class="lk-chart-header">
                    <div class="lk-chart-title">
                        <i class="ph ph-chart-bar"></i>
                        <span>Grafik Laba Rugi Bulanan (Tahun {{ $year }})</span>
                    </div>
                    <span class="lk-chart-badge">Pemasukan vs Beban</span>
                </div>
                <div class="lk-chart-container">
                    <canvas id="profitChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Komposisi Beban Pengeluaran (Donut) -->
            <div class="lk-chart-card">
                <div class="lk-chart-header">
                    <div class="lk-chart-title">
                        <i class="ph ph-chart-donut"></i>
                        <span>Struktur Komposisi Biaya</span>
                    </div>
                    <span class="lk-chart-badge">Kategori</span>
                </div>
                <div class="lk-chart-container">
                    <canvas id="expenseDonutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 3: Arus Kas Riil (Cash In vs Cash Out) -->
        <div class="lk-chart-card">
            <div class="lk-chart-header">
                <div class="lk-chart-title">
                    <i class="ph ph-chart-line-up"></i>
                    <span>Tren Arus Kas (Cash Flow Trend - Realisasi Pembayaran Masuk vs Beban Keluar)</span>
                </div>
                <span class="lk-chart-badge" style="color: #2ecc71; background: rgba(46, 204, 113, 0.12);">Kas Riil</span>
            </div>
            <div class="lk-chart-container">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>

        <!-- Laporan Laba Rugi Formal (Income Statement Table) -->
        @php $pnl = $this->incomeStatementRows; @endphp
        <div class="lk-table-card">
            <div class="lk-table-header">
                <div>
                    <div class="lk-table-title">
                        <i class="ph ph-file-text"></i>
                        <span>Laporan Laba Rugi (P&L Income Statement)</span>
                    </div>
                    <span style="font-size: 12px; color: #8b92a4;">
                        Periode: <b>{{ $this->periodeLabel }}</b> &bull; Metode: <b>{{ strtoupper($viewMode) }}</b>
                    </span>
                </div>
                <div style="font-family: monospace; font-size: 13px; color: #cfa24b;">
                    Mata Uang: IDR (Rp)
                </div>
            </div>

            <table class="lk-pl-table">
                <thead>
                    <tr>
                        <th style="width: 60%;">Deskripsi Akun / Pos Finansial</th>
                        <th class="right" style="width: 40%;">Jumlah (Rupiah)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Bagian I: PENDAPATAN -->
                    <tr class="lk-pl-row-header">
                        <td colspan="2"><i class="ph ph-arrow-circle-down-right"></i> I. PENDAPATAN USAHA</td>
                    </tr>
                    @if($viewMode === 'akrual')
                        <tr>
                            <td style="padding-left: 28px;">Pendapatan Kontrak Pesanan Acara (Gross)</td>
                            <td class="right">Rp {{ number_format($pnl['pendapatan']['omzet_order'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="lk-pl-row-total">
                            <td style="padding-left: 28px;"><b>Total Pendapatan Usaha (Akrual)</b></td>
                            <td class="right" style="color: #cfa24b;">Rp {{ number_format($pnl['pendapatan']['omzet_order'], 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td style="padding-left: 28px;">Realisasi Pembayaran Kas Diterima (DP & Pelunasan)</td>
                            <td class="right">Rp {{ number_format($pnl['pendapatan']['kas_diterima'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 28px; color: #8b92a4;"><i>Catatan: Piutang Pelanggan Berjalan Belum Tertagih</i></td>
                            <td class="right" style="color: #8b92a4;"><i>Rp {{ number_format($pnl['pendapatan']['piutang'], 0, ',', '.') }}</i></td>
                        </tr>
                        <tr class="lk-pl-row-total">
                            <td style="padding-left: 28px;"><b>Total Kas Diterima (Basis Kas)</b></td>
                            <td class="right" style="color: #cfa24b;">Rp {{ number_format($pnl['pendapatan']['kas_diterima'], 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    <!-- Bagian II: BEBAN OPERASIONAL -->
                    <tr class="lk-pl-row-header">
                        <td colspan="2"><i class="ph ph-arrow-circle-up-right"></i> II. BEBAN OPERASIONAL (EXPENSES)</td>
                    </tr>
                    @forelse($pnl['beban_operasional'] as $kategori => $nominal)
                        <tr>
                            <td style="padding-left: 28px;">{{ $kategori }}</td>
                            <td class="right">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="padding-left: 28px; color: #64748b;"><i>Belum ada pengeluaran operasional tercatat pada periode ini</i></td>
                            <td class="right" style="color: #64748b;">Rp 0</td>
                        </tr>
                    @endforelse
                    <tr class="lk-pl-row-total">
                        <td style="padding-left: 28px;"><b>Subtotal Beban Operasional</b></td>
                        <td class="right" style="color: #e74c3c;">Rp {{ number_format($kpi['totalBebanOperasional'], 0, ',', '.') }}</td>
                    </tr>

                    <!-- Bagian III: BEBAN SDM / PAYROLL -->
                    <tr class="lk-pl-row-header">
                        <td colspan="2"><i class="ph ph-users-three"></i> III. BEBAN SUMBER DAYA MANUSIA (PAYROLL)</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 28px;">Beban Gaji Staf Kantor & Upah Kru Lapangan</td>
                        <td class="right">Rp {{ number_format($kpi['totalBebanPayroll'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="lk-pl-row-total">
                        <td style="padding-left: 28px;"><b>Subtotal Beban SDM & Penggajian</b></td>
                        <td class="right" style="color: #e74c3c;">Rp {{ number_format($kpi['totalBebanPayroll'], 0, ',', '.') }}</td>
                    </tr>

                    <!-- TOTAL SEMUA BEBAN -->
                    <tr class="lk-pl-row-total" style="background-color: rgba(231, 76, 60, 0.08);">
                        <td><b>TOTAL BEBAN PENGELUARAN (II + III)</b></td>
                        <td class="right" style="color: #e74c3c; font-size: 14px;">(Rp {{ number_format($kpi['totalBeban'], 0, ',', '.') }})</td>
                    </tr>

                    <!-- Bagian IV: LABA / RUGI BERSIH -->
                    <tr class="lk-pl-row-net">
                        <td>
                            <div><b>IV. LABA / (RUGI) BERSIH OPERASIONAL</b></div>
                            <div style="font-size: 11px; font-weight: normal; color: #8b92a4; margin-top: 2px;">
                                Total Pendapatan &minus; Total Seluruh Beban
                            </div>
                        </td>
                        <td class="right" style="color: {{ $kpi['labaBersih'] >= 0 ? '#2ecc71' : '#e74c3c' }}; font-size: 17px;">
                            Rp {{ number_format($kpi['labaBersih'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Script Chart.js Initialization & Reactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initFinancialCharts();
        });

        document.addEventListener('livewire:navigated', function () {
            initFinancialCharts();
        });

        // Tangkap event update livewire untuk re-render chart saat filter tahun/bulan diubah
        window.addEventListener('update-financial-charts', function () {
            setTimeout(initFinancialCharts, 100);
        });

        let profitChartInstance = null;
        let expenseDonutInstance = null;
        let cashflowChartInstance = null;

        function initFinancialCharts() {
            const monthlyData = @json($this->monthlyChartData);
            const expenseCatData = @json($this->expenseCategoryData);
            const viewMode = @json($this->viewMode);

            // 1. Chart Laba Rugi Bulanan
            const ctxProfit = document.getElementById('profitChart');
            if (ctxProfit) {
                if (profitChartInstance) profitChartInstance.destroy();

                const incomeDataset = viewMode === 'kas' ? monthlyData.kasMasuk : monthlyData.nilaiOrder;
                const netProfitDataset = viewMode === 'kas' ? monthlyData.labaBersihKas : monthlyData.labaBersihAkrual;

                profitChartInstance = new Chart(ctxProfit, {
                    type: 'bar',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [
                            {
                                label: viewMode === 'kas' ? 'Kas Masuk Diterima' : 'Nilai Omzet Order',
                                data: incomeDataset,
                                backgroundColor: 'rgba(207, 162, 75, 0.85)',
                                borderColor: '#cfa24b',
                                borderWidth: 1,
                                borderRadius: 4,
                                order: 2
                            },
                            {
                                label: 'Total Beban (Ops + Gaji)',
                                data: monthlyData.totalBeban,
                                backgroundColor: 'rgba(231, 76, 60, 0.75)',
                                borderColor: '#e74c3c',
                                borderWidth: 1,
                                borderRadius: 4,
                                order: 2
                            },
                            {
                                label: 'Laba Bersih',
                                data: netProfitDataset,
                                type: 'line',
                                borderColor: '#2ecc71',
                                backgroundColor: 'rgba(46, 204, 113, 0.2)',
                                borderWidth: 2.5,
                                tension: 0.3,
                                fill: false,
                                pointRadius: 4,
                                pointBackgroundColor: '#2ecc71',
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#cbd5e1', font: { family: 'Inter', size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.parsed.y || 0;
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#8b92a4', font: { family: 'Inter', size: 11 } },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            },
                            y: {
                                ticks: {
                                    color: '#8b92a4',
                                    font: { family: 'monospace', size: 10 },
                                    callback: function(value) {
                                        if (Math.abs(value) >= 1000000000) return (value / 1000000000).toFixed(1) + 'M';
                                        if (Math.abs(value) >= 1000000) return (value / 1000000).toFixed(0) + 'Jt';
                                        return value;
                                    }
                                },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            }
                        }
                    }
                });
            }

            // 2. Chart Komposisi Pengeluaran (Donut)
            const ctxDonut = document.getElementById('expenseDonutChart');
            if (ctxDonut) {
                if (expenseDonutInstance) expenseDonutInstance.destroy();

                const hasData = expenseCatData.values && expenseCatData.values.length > 0 && expenseCatData.values.some(v => v > 0);

                if (!hasData) {
                    ctxDonut.parentElement.innerHTML = `
                        <div class="lk-empty-chart">
                            <i class="ph ph-receipt"></i>
                            <div>Belum ada data pengeluaran pada periode ini.</div>
                        </div>
                    `;
                } else {
                    expenseDonutInstance = new Chart(ctxDonut, {
                        type: 'doughnut',
                        data: {
                            labels: expenseCatData.labels,
                            datasets: [{
                                data: expenseCatData.values,
                                backgroundColor: expenseCatData.colors,
                                borderColor: '#15161d',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: '#cbd5e1', font: { family: 'Inter', size: 10 }, boxWidth: 12 }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let val = context.raw || 0;
                                            return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
            }

            // 3. Chart Arus Kas (Cash Flow Trend)
            const ctxCashflow = document.getElementById('cashflowChart');
            if (ctxCashflow) {
                if (cashflowChartInstance) cashflowChartInstance.destroy();

                cashflowChartInstance = new Chart(ctxCashflow, {
                    type: 'line',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [
                            {
                                label: 'Arus Kas Masuk (Cash In - Realisasi Bayar)',
                                data: monthlyData.kasMasuk,
                                borderColor: '#2ecc71',
                                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#2ecc71',
                            },
                            {
                                label: 'Arus Kas Keluar (Cash Out - Biaya & Gaji)',
                                data: monthlyData.totalBeban,
                                borderColor: '#e74c3c',
                                backgroundColor: 'rgba(231, 76, 60, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#e74c3c',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#cbd5e1', font: { family: 'Inter', size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.parsed.y || 0;
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#8b92a4', font: { family: 'Inter', size: 11 } },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            },
                            y: {
                                ticks: {
                                    color: '#8b92a4',
                                    font: { family: 'monospace', size: 10 },
                                    callback: function(value) {
                                        if (Math.abs(value) >= 1000000000) return (value / 1000000000).toFixed(1) + 'M';
                                        if (Math.abs(value) >= 1000000) return (value / 1000000).toFixed(0) + 'Jt';
                                        return value;
                                    }
                                },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            }
                        }
                    }
                });
            }
        }

        function laporanKeuanganApp() {
            return {
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

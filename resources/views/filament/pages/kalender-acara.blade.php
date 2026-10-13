<x-filament-panels::page>
    <style>
        .cal-container {
            --bg-card: #15161d;
            --bg-hover: #1c1e28;
            --border-subtle: #20232e;
            --accent-gold: #cfa24b;
            --accent-gold-hover: #deb25b;
            --accent-gold-glow: rgba(207, 162, 75, 0.15);
            --text-primary: #f2f3f7;
            --text-secondary: #8b92a4;
            --text-muted: #5e6475;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-primary);
        }

        .cal-header-bar {
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .cal-nav-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cal-month-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 600;
            color: var(--accent-gold);
            margin: 0;
            min-width: 200px;
        }

        .cal-btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #101116;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s;
        }
        .cal-btn-icon:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
            background: rgba(207, 162, 75, 0.08);
        }

        .cal-btn-pill {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            background: #101116;
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .cal-btn-pill:hover {
            color: var(--text-primary);
            border-color: rgba(207, 162, 75, 0.4);
        }
        .cal-btn-pill.active {
            background: var(--accent-gold);
            color: #0c0d11;
            font-weight: 600;
            border-color: var(--accent-gold);
        }

        .cal-select {
            background: #101116;
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }
        .cal-select:focus {
            border-color: var(--accent-gold);
        }

        /* KPI Cards */
        .cal-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        @media (max-width: 1200px) { .cal-kpi-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .cal-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .cal-kpi-grid { grid-template-columns: 1fr; } }

        .cal-kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            transition: transform 0.15s, border-color 0.15s;
        }
        .cal-kpi-card:hover {
            border-color: rgba(207, 162, 75, 0.3);
            transform: translateY(-2px);
        }
        .cal-kpi-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-secondary);
        }
        .cal-kpi-value {
            font-size: 22px;
            font-weight: 700;
            font-family: monospace;
            color: var(--text-primary);
        }
        .cal-kpi-value.gold { color: var(--accent-gold); }
        .cal-kpi-value.blue { color: #60a5fa; }
        .cal-kpi-value.green { color: #34d399; }
        .cal-kpi-value.amber { color: #fbbf24; }

        /* Filter bar */
        .cal-filter-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }
        .cal-search-input {
            background: #101116;
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 8px 14px 8px 36px;
            font-size: 13px;
            width: 320px;
            max-width: 100%;
            outline: none;
        }
        .cal-search-input:focus {
            border-color: var(--accent-gold);
        }

        /* Calendar Grid */
        .cal-grid-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .cal-days-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #101116;
            border-bottom: 1px solid var(--border-subtle);
        }
        .cal-day-name {
            padding: 12px 8px;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-secondary);
        }
        .cal-day-name.weekend {
            color: var(--accent-gold);
        }

        .cal-weeks {
            display: flex;
            flex-direction: column;
        }
        .cal-week-row {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-bottom: 1px solid var(--border-subtle);
            min-height: 120px;
        }
        .cal-week-row:last-child {
            border-bottom: none;
        }

        .cal-cell {
            border-right: 1px solid var(--border-subtle);
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: var(--bg-card);
            transition: background 0.15s;
            position: relative;
            min-height: 120px;
            cursor: pointer;
        }
        .cal-cell:last-child {
            border-right: none;
        }
        .cal-cell:hover {
            background: var(--bg-hover);
        }
        .cal-cell.other-month {
            opacity: 0.35;
            background: #0f1015;
        }
        .cal-cell.today {
            background: rgba(207, 162, 75, 0.04);
            box-shadow: inset 0 0 0 1px rgba(207, 162, 75, 0.4);
        }
        .cal-cell.selected {
            background: rgba(207, 162, 75, 0.08);
            box-shadow: inset 0 0 0 2px var(--accent-gold);
        }

        .cal-cell-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .cal-day-num {
            font-size: 13px;
            font-weight: 600;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--text-primary);
        }
        .cal-cell.today .cal-day-num {
            background: var(--accent-gold);
            color: #0c0d11;
            font-weight: 700;
        }

        .cal-peak-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        /* Event pills inside cells */
        .cal-event-pill {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            line-height: 1.3;
            display: flex;
            flex-direction: column;
            gap: 1px;
            cursor: pointer;
            transition: all 0.15s;
            border-left: 3px solid transparent;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .cal-event-pill:hover {
            transform: translateX(2px);
            filter: brightness(1.15);
        }

        /* Status Colors */
        .status-masuk {
            background: rgba(96, 165, 250, 0.12);
            color: #93c5fd;
            border-left-color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-left-width: 3px;
        }
        .status-proses {
            background: rgba(207, 162, 75, 0.15);
            color: #ffd88a;
            border-left-color: var(--accent-gold);
            border: 1px solid rgba(207, 162, 75, 0.3);
            border-left-width: 3px;
        }
        .status-selesai {
            background: rgba(52, 211, 153, 0.12);
            color: #6ee7b7;
            border-left-color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-left-width: 3px;
        }
        .status-batal {
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border-left-color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-left-width: 3px;
        }

        .cal-prep-pill {
            background: rgba(168, 85, 247, 0.12);
            color: #c084fc;
            border-left-color: #a855f7;
            border: 1px dashed rgba(168, 85, 247, 0.3);
            border-left-width: 3px;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 4px;
        }

        /* Week view cards */
        .cal-week-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
        }
        @media (max-width: 1024px) { .cal-week-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .cal-week-grid { grid-template-columns: 1fr; } }

        .cal-week-day-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 14px;
            min-height: 280px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .cal-week-day-card.today {
            border-color: var(--accent-gold);
            background: rgba(207, 162, 75, 0.03);
        }

        /* Agenda List View */
        .cal-agenda-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .cal-agenda-item {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: all 0.2s;
        }
        .cal-agenda-item:hover {
            border-color: rgba(207, 162, 75, 0.4);
            transform: translateX(4px);
        }
        @media (max-width: 768px) {
            .cal-agenda-item { flex-direction: column; align-items: flex-start; }
        }

        /* Modal Details */
        .cal-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 6, 9, 0.75);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .cal-modal-card {
            background: #15161e;
            border: 1px solid #2d3140;
            border-radius: 16px;
            width: 680px;
            max-width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            color: var(--text-primary);
        }
        .cal-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            background: #111218;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .cal-modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .cal-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-subtle);
            background: #111218;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cal-detail-box {
            background: #101117;
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 16px;
        }
        .cal-detail-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 4px;
        }
    </style>

    <div class="cal-container">
        <!-- Top Navigation Bar -->
        <div class="cal-header-bar">
            <!-- Left: Month Title & Navigation -->
            <div class="cal-nav-group">
                <button type="button" wire:click="previousMonth" class="cal-btn-icon" title="Bulan Sebelumnya">
                    <i class="ph ph-caret-left" style="font-size: 18px;"></i>
                </button>

                <h2 class="cal-month-title">{{ $this->indonesian_month_name }}</h2>

                <button type="button" wire:click="nextMonth" class="cal-btn-icon" title="Bulan Berikutnya">
                    <i class="ph ph-caret-right" style="font-size: 18px;"></i>
                </button>

                <button type="button" wire:click="today" class="cal-btn-pill" title="Kembali ke Hari Ini">
                    <i class="ph ph-calendar-check" style="font-size: 16px; color: var(--accent-gold);"></i>
                    Hari Ini
                </button>

                <!-- Month & Year Jump Selectors -->
                <select wire:model.live="month" class="cal-select">
                    @foreach($this->available_months as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>

                <select wire:model.live="year" class="cal-select">
                    @foreach($this->available_years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Right: View Mode & Create Action -->
            <div class="cal-nav-group">
                <div style="display: flex; background: #101116; border: 1px solid var(--border-subtle); border-radius: 8px; padding: 2px;">
                    <button type="button" wire:click="setViewMode('month')" class="cal-btn-pill {{ $viewMode === 'month' ? 'active' : '' }}" style="border: none; border-radius: 6px;">
                        <i class="ph ph-squares-four"></i> Bulan
                    </button>
                    <button type="button" wire:click="setViewMode('week')" class="cal-btn-pill {{ $viewMode === 'week' ? 'active' : '' }}" style="border: none; border-radius: 6px;">
                        <i class="ph ph-calendar"></i> Minggu
                    </button>
                    <button type="button" wire:click="setViewMode('agenda')" class="cal-btn-pill {{ $viewMode === 'agenda' ? 'active' : '' }}" style="border: none; border-radius: 6px;">
                        <i class="ph ph-list-bullets"></i> Agenda
                    </button>
                </div>

                <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-secondary); cursor: pointer;">
                    <input type="checkbox" wire:model.live="showPrepDates" style="accent-color: var(--accent-gold); cursor: pointer;">
                    <span>Tampilkan SPK Persiapan</span>
                </label>

                <a href="{{ \App\Filament\Resources\OrderResource::getUrl('create', ['tanggal' => $selectedDate ?? now()->toDateString()]) }}" class="cal-btn-pill active" style="text-decoration: none;">
                    <i class="ph ph-plus-circle" style="font-size: 16px;"></i>
                    Pesanan Baru
                </a>
            </div>
        </div>

        <!-- KPI Stats Cards -->
        @php
            $stats = $this->month_stats;
        @endphp
        <div class="cal-kpi-grid">
            <div class="cal-kpi-card">
                <div class="cal-kpi-label">TOTAL ACARA BULAN INI</div>
                <div class="cal-kpi-value gold">{{ $stats['total'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">Acara</span></div>
            </div>
            <div class="cal-kpi-card">
                <div class="cal-kpi-label">PESANAN MASUK</div>
                <div class="cal-kpi-value blue">{{ $stats['masuk'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">Menunggu</span></div>
            </div>
            <div class="cal-kpi-card">
                <div class="cal-kpi-label">DALAM PROSES / PERSIAPAN</div>
                <div class="cal-kpi-value amber">{{ $stats['proses'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">Berjalan</span></div>
            </div>
            <div class="cal-kpi-card">
                <div class="cal-kpi-label">ACARA SELESAI</div>
                <div class="cal-kpi-value green">{{ $stats['selesai'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">Tuntas</span></div>
            </div>
            <div class="cal-kpi-card">
                <div class="cal-kpi-label">ESTIMASI NILAI OMSET</div>
                <div class="cal-kpi-value gold" style="font-size: 18px;">Rp {{ number_format($stats['nominal'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="cal-filter-bar">
            <!-- Search -->
            <div style="position: relative; display: flex; align-items: center;">
                <i class="ph ph-magnifying-glass" style="position: absolute; left: 12px; color: var(--text-muted); font-size: 16px;"></i>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari acara, customer, venue, no order..." 
                    class="cal-search-input"
                >
            </div>

            <!-- Status filter pills -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" wire:click="$set('filterStatus', 'all')" class="cal-btn-pill {{ $filterStatus === 'all' ? 'active' : '' }}">
                    Semua
                </button>
                <button type="button" wire:click="$set('filterStatus', 'pesanan masuk')" class="cal-btn-pill {{ $filterStatus === 'pesanan masuk' ? 'active' : '' }}">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #60a5fa; display: inline-block;"></span>
                    Masuk
                </button>
                <button type="button" wire:click="$set('filterStatus', 'proses')" class="cal-btn-pill {{ $filterStatus === 'proses' ? 'active' : '' }}">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-gold); display: inline-block;"></span>
                    Proses
                </button>
                <button type="button" wire:click="$set('filterStatus', 'selesai')" class="cal-btn-pill {{ $filterStatus === 'selesai' ? 'active' : '' }}">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                    Selesai
                </button>
            </div>

            <!-- Jenis Acara Filter -->
            @if(count($this->jenis_acara_list) > 0)
                <select wire:model.live="filterJenis" class="cal-select">
                    <option value="all">Semua Jenis Acara</option>
                    @foreach($this->jenis_acara_list as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- 1. MONTH VIEW -->
        @if($viewMode === 'month')
            <div class="cal-grid-wrapper">
                <!-- Day Names Header -->
                <div class="cal-days-header">
                    <div class="cal-day-name">SENIN</div>
                    <div class="cal-day-name">SELASA</div>
                    <div class="cal-day-name">RABU</div>
                    <div class="cal-day-name">KAMIS</div>
                    <div class="cal-day-name">JUMAT</div>
                    <div class="cal-day-name weekend">SABTU</div>
                    <div class="cal-day-name weekend">MINGGU</div>
                </div>

                <!-- Calendar Weeks Grid -->
                <div class="cal-weeks">
                    @foreach($this->calendar_weeks as $week)
                        <div class="cal-week-row">
                            @foreach($week as $day)
                                <div 
                                    class="cal-cell {{ !$day['isCurrentMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }} {{ $day['isSelected'] ? 'selected' : '' }}"
                                    wire:click="selectDate('{{ $day['date'] }}')"
                                >
                                    <div class="cal-cell-header">
                                        <span class="cal-day-num">{{ $day['dayNumber'] }}</span>
                                        @if($day['isPeak'])
                                            <span class="cal-peak-badge" title="Hari Padat: {{ $day['orders']->count() }} acara">
                                                <i class="ph ph-lightning"></i> {{ $day['orders']->count() }} Acara
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Event Pills -->
                                    <div style="display: flex; flex-direction: column; gap: 4px; overflow: hidden;">
                                        @foreach($day['orders'] as $order)
                                            @php
                                                $statusClass = match(strtolower($order->status)) {
                                                    'selesai' => 'status-selesai',
                                                    'proses', 'persiapan', 'dikonfirmasi' => 'status-proses',
                                                    'dibatalkan' => 'status-batal',
                                                    default => 'status-masuk'
                                                };
                                            @endphp
                                            <div 
                                                class="cal-event-pill {{ $statusClass }}"
                                                wire:click.stop="selectOrder({{ $order->id }})"
                                                title="{{ $order->nama_acara }} ({{ $order->customer?->nama }})"
                                            >
                                                <div style="font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                                                    <span style="overflow: hidden; text-overflow: ellipsis;">{{ $order->nama_acara }}</span>
                                                    @if($order->jam)
                                                        <span style="font-family: monospace; font-size: 10px; opacity: 0.85; margin-left: 4px;">{{ substr($order->jam, 0, 5) }}</span>
                                                    @endif
                                                </div>
                                                <div style="font-size: 10px; opacity: 0.85; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $order->customer?->nama ?? 'Umum' }} &middot; {{ $order->venue ?? 'Venue belum diisi' }}
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Work Order Preps (if any) -->
                                        @if($showPrepDates && $day['preps']->count() > 0)
                                            @foreach($day['preps'] as $prep)
                                                <div 
                                                    class="cal-prep-pill"
                                                    wire:click.stop="selectOrder({{ $prep->order_id }})"
                                                    title="Persiapan SPK: {{ $prep->spk_number }}"
                                                >
                                                    <i class="ph ph-truck" style="margin-right: 2px;"></i>
                                                    <b>Persiapan SPK:</b> {{ $prep->spk_number }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- 2. WEEK VIEW -->
        @elseif($viewMode === 'week')
            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <button type="button" wire:click="previousWeek" class="cal-btn-pill">
                        <i class="ph ph-arrow-left"></i> Minggu Sebelumnya
                    </button>
                    <span style="color: var(--accent-gold); font-weight: 600; font-size: 14px;">
                        Tampilan Mingguan
                    </span>
                    <button type="button" wire:click="nextWeek" class="cal-btn-pill">
                        Minggu Berikutnya <i class="ph ph-arrow-right"></i>
                    </button>
                </div>

                <div class="cal-week-grid">
                    @foreach($this->week_days as $wDay)
                        <div class="cal-week-day-card {{ $wDay['isToday'] ? 'today' : '' }}">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                                <div>
                                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">
                                        {{ $wDay['dayName'] }}
                                    </div>
                                    <div style="font-size: 18px; font-weight: 700; color: {{ $wDay['isToday'] ? 'var(--accent-gold)' : 'var(--text-primary)' }};">
                                        {{ $wDay['dayNumber'] }} {{ $wDay['monthName'] }}
                                    </div>
                                </div>
                                <span style="font-size: 11px; padding: 2px 8px; border-radius: 100px; background: #101116; color: var(--text-secondary);">
                                    {{ $wDay['orders']->count() }} Acara
                                </span>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px; flex: 1;">
                                @forelse($wDay['orders'] as $order)
                                    @php
                                        $statusClass = match(strtolower($order->status)) {
                                            'selesai' => 'status-selesai',
                                            'proses', 'persiapan', 'dikonfirmasi' => 'status-proses',
                                            'dibatalkan' => 'status-batal',
                                            default => 'status-masuk'
                                        };
                                    @endphp
                                    <div 
                                        class="cal-event-pill {{ $statusClass }}" 
                                        style="padding: 10px; border-radius: 8px;"
                                        wire:click="selectOrder({{ $order->id }})"
                                    >
                                        <div style="font-weight: 600; font-size: 13px; margin-bottom: 4px;">{{ $order->nama_acara }}</div>
                                        <div style="font-size: 11px; color: var(--text-secondary);">
                                            <i class="ph ph-clock"></i> {{ $order->jam ? substr($order->jam, 0, 5) : 'Waktu TBA' }} &bull;
                                            <i class="ph ph-user"></i> {{ $order->customer?->nama }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">
                                            <i class="ph ph-map-pin"></i> {{ $order->venue ?? '-' }}
                                        </div>
                                    </div>
                                @empty
                                    <div style="text-align: center; color: var(--text-muted); font-size: 12px; margin-top: 30px;">
                                        Tidak ada acara
                                    </div>
                                @endforelse

                                @if($showPrepDates && $wDay['preps']->count() > 0)
                                    <div style="margin-top: 8px; border-top: 1px dashed var(--border-subtle); padding-top: 8px;">
                                        <div style="font-size: 10px; color: #c084fc; font-weight: 600; margin-bottom: 4px;">SPK PERSIAPAN:</div>
                                        @foreach($wDay['preps'] as $prep)
                                            <div class="cal-prep-pill" wire:click="selectOrder({{ $prep->order_id }})">
                                                {{ $prep->spk_number }} &middot; {{ $prep->order?->nama_acara }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- 3. AGENDA LIST VIEW -->
        @elseif($viewMode === 'agenda')
            <div class="cal-agenda-list" style="margin-bottom: 24px;">
                @forelse($this->agenda_orders as $order)
                    @php
                        $statusClass = match(strtolower($order->status)) {
                            'selesai' => 'status-selesai',
                            'proses', 'persiapan', 'dikonfirmasi' => 'status-proses',
                            'dibatalkan' => 'status-batal',
                            default => 'status-masuk'
                        };
                        $carbonDate = \Carbon\Carbon::parse($order->tanggal);
                    @endphp
                    <div class="cal-agenda-item">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <!-- Date badge -->
                            <div style="text-align: center; background: #101116; border: 1px solid var(--border-subtle); border-radius: 10px; padding: 10px 16px; min-width: 80px;">
                                <div style="font-size: 11px; text-transform: uppercase; color: var(--accent-gold); font-weight: 600;">
                                    {{ $carbonDate->format('M') }}
                                </div>
                                <div style="font-size: 26px; font-weight: 700; line-height: 1; margin: 4px 0;">
                                    {{ $carbonDate->day }}
                                </div>
                                <div style="font-size: 10px; color: var(--text-secondary);">
                                    {{ ['Min','Sen','Sel','Rab','Kam','Jum','Sab'][$carbonDate->dayOfWeek] }}
                                </div>
                            </div>

                            <!-- Order info -->
                            <div>
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                    <h4 style="font-size: 16px; font-weight: 600; margin: 0; color: var(--text-primary);">
                                        {{ $order->nama_acara }}
                                    </h4>
                                    <span style="font-family: monospace; font-size: 12px; color: var(--accent-gold);">
                                        {{ $order->no_order }}
                                    </span>
                                    <span class="cal-event-pill {{ $statusClass }}" style="padding: 2px 10px; border-radius: 100px; font-size: 10px; text-transform: uppercase; font-weight: 600;">
                                        {{ $order->status }}
                                    </span>
                                </div>

                                <div style="font-size: 13px; color: var(--text-secondary); display: flex; flex-wrap: wrap; gap: 14px;">
                                    <span><i class="ph ph-clock" style="color: var(--accent-gold);"></i> {{ $order->jam ? substr($order->jam, 0, 5) : 'Waktu TBA' }}</span>
                                    <span><i class="ph ph-user" style="color: var(--accent-gold);"></i> {{ $order->customer?->nama ?? 'Tanpa Nama' }}</span>
                                    <span><i class="ph ph-phone" style="color: var(--accent-gold);"></i> {{ $order->customer?->no_telp ?? '-' }}</span>
                                    <span><i class="ph ph-map-pin" style="color: var(--accent-gold);"></i> {{ $order->venue ?? 'Venue belum diisi' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Total & Action -->
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="text-align: right;">
                                <div style="font-size: 10px; color: var(--text-secondary); text-transform: uppercase;">Total Biaya</div>
                                <div style="font-size: 17px; font-weight: 700; font-family: monospace; color: var(--accent-gold);">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </div>
                            </div>
                            <button type="button" wire:click="selectOrder({{ $order->id }})" class="cal-btn-pill active">
                                <i class="ph ph-eye"></i> Detail
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 12px; padding: 40px; text-align: center; color: var(--text-secondary);">
                        <i class="ph ph-calendar-x" style="font-size: 36px; color: var(--text-muted); margin-bottom: 8px;"></i>
                        <p style="margin: 0; font-size: 14px;">Tidak ada agenda acara pada bulan/filter ini.</p>
                    </div>
                @endforelse
            </div>
        @endif

        <!-- SELECTED DATE BREAKDOWN DRAWER (If a date is selected and in Month View) -->
        @if($viewMode === 'month' && $selectedDate)
            @php
                $dayEvents = $this->selected_date_events;
                $formattedSelectedDate = \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y');
            @endphp
            <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 12px; padding: 20px 24px; margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--accent-gold); font-weight: 600;">JADWAL TANGGAL TERPILIH</span>
                        <h3 style="font-size: 18px; font-weight: 600; margin: 2px 0 0 0; color: var(--text-primary);">
                            {{ $formattedSelectedDate }}
                        </h3>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="/orders/create" class="cal-btn-pill">
                            <i class="ph ph-plus"></i> Tambah Acara di Tanggal Ini
                        </a>
                        <button type="button" wire:click="$set('selectedDate', null)" class="cal-btn-icon" title="Tutup">
                            <i class="ph ph-x"></i>
                        </button>
                    </div>
                </div>

                @if($dayEvents->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px;">
                        @foreach($dayEvents as $evt)
                            @php
                                $statusClass = match(strtolower($evt->status)) {
                                    'selesai' => 'status-selesai',
                                    'proses', 'persiapan', 'dikonfirmasi' => 'status-proses',
                                    'dibatalkan' => 'status-batal',
                                    default => 'status-masuk'
                                };
                            @endphp
                            <div class="cal-detail-box" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" wire:click="selectOrder({{ $evt->id }})">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <b style="color: var(--text-primary); font-size: 14px;">{{ $evt->nama_acara }}</b>
                                        <span class="cal-event-pill {{ $statusClass }}" style="padding: 2px 8px; font-size: 10px;">{{ $evt->status }}</span>
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-secondary);">
                                        <i class="ph ph-clock"></i> {{ $evt->jam ? substr($evt->jam, 0, 5) : 'Waktu TBA' }} &bull;
                                        <i class="ph ph-user"></i> {{ $evt->customer?->nama }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                        <i class="ph ph-map-pin"></i> {{ $evt->venue ?? 'Venue belum diisi' }}
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 14px; font-weight: 700; color: var(--accent-gold); font-family: monospace;">
                                        Rp {{ number_format($evt->total, 0, ',', '.') }}
                                    </div>
                                    <span style="font-size: 11px; color: var(--accent-gold); text-decoration: underline; margin-top: 4px; display: inline-block;">Lihat &rarr;</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px;">
                        Tidak ada acara yang dijadwalkan pada tanggal ini.
                    </div>
                @endif
            </div>
        @endif

        <!-- INTERACTIVE ORDER DETAIL MODAL -->
        @if($selectedOrderId && $this->selected_order_data)
            @php
                $order = $this->selected_order_data;
                $statusClass = match(strtolower($order->status)) {
                    'selesai' => 'status-selesai',
                    'proses', 'persiapan', 'dikonfirmasi' => 'status-proses',
                    'dibatalkan' => 'status-batal',
                    default => 'status-masuk'
                };
            @endphp
            <div class="cal-modal-overlay" wire:click.self="closeModal">
                <div class="cal-modal-card">
                    <!-- Modal Header -->
                    <div class="cal-modal-header">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--accent-gold); font-weight: 600;">
                                DETAIL ACARA & PESANAN
                            </div>
                            <h3 style="font-size: 20px; font-weight: 700; margin: 4px 0 2px 0; color: var(--text-primary); font-family: 'Playfair Display', serif;">
                                {{ $order->nama_acara }}
                            </h3>
                            <div style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">
                                <span style="font-family: monospace; font-size: 12px; color: var(--accent-gold); background: #0c0d11; padding: 2px 8px; border-radius: 4px; border: 1px solid var(--border-subtle);">
                                    {{ $order->no_order }}
                                </span>
                                <span class="cal-event-pill {{ $statusClass }}" style="padding: 3px 10px; border-radius: 100px; font-size: 10px; text-transform: uppercase; font-weight: 600;">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="cal-btn-icon" style="border-radius: 50%;">
                            <i class="ph ph-x" style="font-size: 18px;"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="cal-modal-body">
                        <!-- 1. Customer & Venue Details Grid -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div class="cal-detail-box">
                                <div class="cal-detail-label"><i class="ph ph-user"></i> INFORMASI KLIEN</div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--text-primary); margin-bottom: 2px;">
                                    {{ $order->customer?->nama ?? 'Tanpa Nama' }}
                                </div>
                                <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 6px;">
                                    {{ $order->customer?->no_telp ?? '-' }}
                                </div>
                                @if($order->customer?->no_telp)
                                    @php
                                        $phoneClean = preg_replace('/[^0-9]/', '', $order->customer->no_telp);
                                        if(str_starts_with($phoneClean, '0')) {
                                            $phoneClean = '62' . substr($phoneClean, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $phoneClean }}" target="_blank" class="cal-btn-pill" style="font-size: 11px; padding: 4px 10px;">
                                        <i class="ph ph-whatsapp-logo" style="color: #25D366;"></i> Hubungi via WA
                                    </a>
                                @endif
                            </div>

                            <div class="cal-detail-box">
                                <div class="cal-detail-label"><i class="ph ph-map-pin"></i> LOKASI & WAKTU ACARA</div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--text-primary); margin-bottom: 2px;">
                                    {{ $order->venue ?? 'Lokasi belum ditentukan' }}
                                </div>
                                <div style="font-size: 13px; color: var(--text-secondary);">
                                    <i class="ph ph-calendar"></i> {{ \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d F Y') }}
                                </div>
                                <div style="font-size: 13px; color: var(--text-secondary);">
                                    <i class="ph ph-clock"></i> {{ $order->jam ? substr($order->jam, 0, 5) . ' WIB' : 'Waktu TBA' }}
                                </div>
                            </div>
                        </div>

                        <!-- 2. Financial Overview -->
                        <div class="cal-detail-box" style="display: flex; justify-content: space-between; align-items: center; background: rgba(207, 162, 75, 0.05); border-color: rgba(207, 162, 75, 0.2);">
                            <div>
                                <div class="cal-detail-label" style="color: var(--accent-gold);">TOTAL NILAI KONTRAK</div>
                                <div style="font-size: 22px; font-weight: 700; font-family: monospace; color: var(--accent-gold);">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="text-align: right; font-size: 12px; color: var(--text-secondary);">
                                <div>Subtotal: Rp {{ number_format($order->subtotal, 0, ',', '.') }}</div>
                                @if($order->diskon > 0)
                                    <div>Diskon: -Rp {{ number_format($order->diskon, 0, ',', '.') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Work Orders & Crew Operations -->
                        <div class="cal-detail-box">
                            <div class="cal-detail-label" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="ph ph-truck"></i> PERSIAPAN LOGISTIK & PENUGASAN KRU</span>
                                <span style="font-weight: normal; color: var(--text-muted);">{{ $order->workOrders->count() }} SPK Terkait</span>
                            </div>

                            @if($order->workOrders->count() > 0)
                                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                                    @foreach($order->workOrders as $wo)
                                        <div style="background: #15161e; border: 1px solid var(--border-subtle); border-radius: 8px; padding: 12px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                                <div style="font-weight: 600; font-size: 13px; color: var(--accent-gold);">
                                                    {{ $wo->spk_number }}
                                                    <span style="font-size: 11px; color: var(--text-secondary); font-weight: normal; margin-left: 6px;">
                                                        (SJ: {{ $wo->surat_jalan_number ?? '-' }})
                                                    </span>
                                                </div>
                                                <span class="cal-btn-pill" style="font-size: 10px; padding: 2px 8px;">
                                                    {{ $wo->status }}
                                                </span>
                                            </div>

                                            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 6px;">
                                                <b>Tanggal Persiapan:</b> {{ $wo->tanggal_persiapan ? \Carbon\Carbon::parse($wo->tanggal_persiapan)->format('d M Y') : '-' }} &bull;
                                                <b>PIC:</b> {{ $wo->pic?->name ?? 'Belum ditugaskan' }}
                                            </div>

                                            <!-- Kru Bertugas -->
                                            @if($wo->users->count() > 0)
                                                <div style="margin-top: 6px;">
                                                    <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 4px;">Kru yang Ditugaskan:</div>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                                        @foreach($wo->users as $u)
                                                            <span style="background: #1a1c26; border: 1px solid var(--border-subtle); border-radius: 4px; padding: 2px 8px; font-size: 11px;">
                                                                {{ $u->user?->name ?? 'Kru' }} <span style="color: var(--accent-gold);">({{ $u->peran ?? 'Staff' }})</span>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- SPK Download Actions -->
                                            <div style="display: flex; gap: 8px; margin-top: 10px;">
                                                <a href="/work-orders/{{ $wo->id }}/spk" target="_blank" class="cal-btn-pill" style="font-size: 11px; padding: 4px 10px;">
                                                    <i class="ph ph-file-pdf"></i> Download SPK
                                                </a>
                                                <a href="/work-orders/{{ $wo->id }}/surat-jalan" target="_blank" class="cal-btn-pill" style="font-size: 11px; padding: 4px 10px;">
                                                    <i class="ph ph-navigation-arrow"></i> Download Surat Jalan
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="text-align: center; padding: 14px; color: var(--text-muted); font-size: 12px;">
                                    Belum ada Surat Perintah Kerja (SPK) untuk pesanan ini.
                                    <div style="margin-top: 6px;">
                                        <a href="/work-orders/create" class="cal-btn-pill" style="font-size: 11px;">
                                            <i class="ph ph-plus"></i> Buat SPK Sekarang
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- 4. Notes if any -->
                        @if($order->catatan_khusus)
                            <div class="cal-detail-box">
                                <div class="cal-detail-label"><i class="ph ph-note"></i> CATATAN KHUSUS</div>
                                <div style="font-size: 13px; color: var(--text-secondary); line-height: 1.4;">
                                    {{ $order->catatan_khusus }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="cal-modal-footer">
                        <a href="/orders/{{ $order->id }}/invoice" target="_blank" class="cal-btn-pill">
                            <i class="ph ph-receipt"></i> Cetak Invoice
                        </a>
                        <a href="/orders/{{ $order->id }}/edit" class="cal-btn-pill active" style="text-decoration: none;">
                            <i class="ph ph-pencil-simple"></i> Buka / Edit Pesanan
                        </a>
                        <button type="button" wire:click="closeModal" class="cal-btn-pill">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

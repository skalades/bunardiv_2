<x-filament-widgets::widget class="custom-widget-wrapper">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&display=swap');
        
        .custom-widget-wrapper {
            box-shadow: none !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .custom-dashboard {
            --bg-card: #151618;
            --text-primary: #f0f0f0;
            --text-secondary: #888888;
            --accent-gold: #cfa24b;
            --success-green: #3ee08a;
            --border-color: #262626;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }
        
        .fi-header-heading { display: none !important; } /* Hide default Dashboard heading to avoid duplicate */
        .font-serif { font-family: 'Playfair Display', serif; }
        
        .c-header { margin-bottom: 32px; }
        .c-header-subtitle { font-size: 11px; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 600; }
        .c-header-title { font-size: 32px; font-weight: 600; letter-spacing: -0.5px; color: var(--accent-gold); margin: 0; line-height: 1.2; }

        .c-grid-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
        @media (max-width: 1024px) { .c-grid-cards { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .c-grid-cards { grid-template-columns: 1fr; } }
        
        .c-card { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; display: flex; flex-direction: column; }
        
        .c-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .c-card-title { font-size: 11px; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 1px; font-weight: 600; }
        .c-card-icon { width: 36px; height: 36px; border-radius: 8px; background-color: #222; display: flex; align-items: center; justify-content: center; color: var(--accent-gold); font-size: 18px; }
        .c-card-value { font-size: 26px; font-weight: 600; margin-bottom: 6px; font-family: monospace; letter-spacing: -0.5px; color: var(--text-primary); }
        .c-card-value.gold { color: var(--accent-gold); }
        .c-card-subtitle { font-size: 12px; color: var(--text-secondary); }

        .c-grid-middle { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
        @media (max-width: 1024px) { .c-grid-middle { grid-template-columns: 1fr; } }
        
        .c-profit-card { display: flex; flex-direction: column; min-height: 240px; }
        .c-card-header-icon { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .c-card-header-icon .title { font-size: 16px; color: var(--accent-gold); font-weight: 500; }
        .c-card-header-icon i { color: var(--accent-gold); font-size: 20px; }
        
        .c-profit-amount { font-size: 52px; font-weight: 600; color: var(--success-green); font-family: monospace; letter-spacing: -1.5px; margin-bottom: 30px; line-height: 1; }
        .c-profit-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: auto; }
        @media (max-width: 640px) { .c-profit-stats { grid-template-columns: 1fr; gap: 12px; } .c-profit-amount { font-size: 36px; } }
        
        .c-stat-item { display: flex; flex-direction: column; gap: 8px; }
        .c-stat-label { font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .c-stat-value { font-size: 15px; font-weight: 600; font-family: monospace; color: var(--text-primary); }
        .c-stat-value.gold { color: var(--accent-gold); }
        .c-stat-value.pink { color: #e5a4a4; }

        .c-notification-list { list-style: none; display: flex; flex-direction: column; gap: 16px; padding: 0; margin: 0; }
        .c-notification-item { display: flex; align-items: center; gap: 14px; font-size: 14px; color: var(--text-secondary); }
        .c-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .c-dot.red { background-color: #ff4d4f; }
        .c-dot.gold { background-color: var(--accent-gold); }
        .c-dot.green { background-color: var(--success-green); }
        .c-notification-item b { font-weight: 600; color: var(--text-primary); }

        .c-grid-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 1024px) { .c-grid-bottom { grid-template-columns: 1fr; } }
        
        .c-schedule-empty { height: 180px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); font-size: 14px; }
        .c-orders-list { display: flex; flex-direction: column; gap: 12px; }
        
        .c-order-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background-color: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 10px; transition: background-color 0.2s; }
        .c-order-item:hover { background-color: rgba(255, 255, 255, 0.04); }
        
        .c-order-info { display: flex; flex-direction: column; gap: 6px; }
        .c-order-info h4 { font-size: 14px; font-weight: 500; color: var(--text-primary); margin: 0; }
        .c-order-info h4 span { color: var(--accent-gold); font-size: 12px; margin-left: 10px; font-family: monospace; font-weight: 400; }
        .c-order-info p { font-size: 12px; color: var(--text-secondary); margin: 0; }
        
        .c-badge { padding: 6px 14px; border-radius: 100px; font-size: 10px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
        .c-badge.masuk { background-color: rgba(255,255,255,0.08); color: var(--text-primary); }
        .c-badge.selesai { background-color: rgba(62, 224, 138, 0.1); color: var(--success-green); border: 1px solid rgba(62, 224, 138, 0.15); }
        .c-badge.dp { background-color: rgba(207, 162, 75, 0.1); color: var(--accent-gold); border: 1px solid rgba(207, 162, 75, 0.15); }
        .c-badge.proses { background-color: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
        .c-badge.batal { background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
    </style>
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @php
        $stats = $this->dashboardStats;
        $recentOrders = $this->recentOrders;
    @endphp

    <div class="custom-dashboard">
        <header class="c-header">
            <div class="c-header-subtitle">DASHBOARD OPERASIONAL</div>
            <h1 class="c-header-title font-serif">Selamat datang kembali.</h1>
        </header>

        <!-- Top Cards -->
        <div class="c-grid-cards">
            <!-- 1. Pesanan Hari Ini -->
            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">PESANAN HARI INI</div>
                    <div class="c-card-icon"><i class="ph ph-bag-check"></i></div>
                </div>
                <div class="c-card-value">{{ $stats['todayOrdersCount'] }}</div>
                <div class="c-card-subtitle">{{ $stats['pendingOrdersCount'] }} menunggu diproses</div>
            </div>
            
            <!-- 2. Total Pemasukan -->
            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">TOTAL PEMASUKAN</div>
                    <div class="c-card-icon"><i class="ph ph-trend-up"></i></div>
                </div>
                <div class="c-card-value gold">Rp {{ number_format($stats['totalPemasukan'], 0, ',', '.') }}</div>
                <div class="c-card-subtitle">{{ $stats['totalPembayaranCount'] }} pembayaran masuk</div>
            </div>

            <!-- 3. Pengeluaran -->
            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">PENGELUARAN</div>
                    <div class="c-card-icon"><i class="ph ph-wallet"></i></div>
                </div>
                <div class="c-card-value gold">Rp {{ number_format($stats['totalPengeluaran'], 0, ',', '.') }}</div>
                <div class="c-card-subtitle">Ops: Rp {{ number_format($stats['bebanOps'], 0, ',', '.') }} &bull; Gaji: Rp {{ number_format($stats['bebanGaji'], 0, ',', '.') }}</div>
            </div>

            <!-- 4. Piutang Pelanggan -->
            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">PIUTANG PELANGGAN</div>
                    <div class="c-card-icon"><i class="ph ph-credit-card"></i></div>
                </div>
                <div class="c-card-value gold">Rp {{ number_format($stats['piutangPelanggan'], 0, ',', '.') }}</div>
                <div class="c-card-subtitle">{{ $stats['unpaidInvoicesCount'] }} pesanan belum lunas</div>
            </div>
        </div>

        <!-- Middle Section -->
        <div class="c-grid-middle">
            <div class="c-card c-profit-card">
                <div class="c-card-header-icon">
                    <div class="title">Estimasi Profit Bulan Ini ({{ now()->translatedFormat('F Y') }})</div>
                    <i class="ph ph-sparkle"></i>
                </div>
                <div class="c-profit-amount" style="color: {{ $stats['profitBulanIni'] >= 0 ? 'var(--success-green)' : '#ef4444' }};">
                    Rp {{ number_format($stats['profitBulanIni'], 0, ',', '.') }}
                </div>
                
                <div class="c-profit-stats">
                    <div class="c-stat-item">
                        <div class="c-stat-label">PEMASUKAN</div>
                        <div class="c-stat-value gold">Rp {{ number_format($stats['pemasukanBulanIni'], 0, ',', '.') }}</div>
                    </div>
                    <div class="c-stat-item">
                        <div class="c-stat-label">PENGELUARAN</div>
                        <div class="c-stat-value pink">- Rp {{ number_format($stats['pengeluaranBulanIni'], 0, ',', '.') }}</div>
                    </div>
                    <div class="c-stat-item">
                        <div class="c-stat-label">PESANAN SELESAI</div>
                        <div class="c-stat-value">{{ $stats['pesananSelesaiBulanIni'] }}</div>
                    </div>
                </div>
            </div>

            <div class="c-card">
                <div class="c-card-header-icon" style="margin-bottom: 20px;">
                    <div class="title" style="color: var(--text-primary);">Notifikasi</div>
                    <i class="ph ph-warning"></i>
                </div>
                <ul class="c-notification-list">
                    <li class="c-notification-item">
                        <div class="c-dot {{ $stats['unpaidInvoicesCount'] > 0 ? 'red' : 'green' }}"></div>
                        <span>
                            <a href="{{ url('/tagihans') }}" style="color: inherit; text-decoration: none;">
                                <b>{{ $stats['unpaidInvoicesCount'] }}</b> invoice/pesanan belum lunas.
                            </a>
                        </span>
                    </li>
                    <li class="c-notification-item">
                        <div class="c-dot gold"></div>
                        <span>
                            <a href="{{ url('/kalender') }}" style="color: inherit; text-decoration: none;">
                                <b>{{ $this->today_orders->count() }}</b> jadwal hari ini.
                                <span style="color: var(--accent-gold); font-size: 11px; margin-left: 4px;">(Lihat Kalender &rarr;)</span>
                            </a>
                        </span>
                    </li>
                    <li class="c-notification-item">
                        <div class="c-dot {{ $stats['crewBelum'] > 0 ? 'gold' : 'green' }}"></div>
                        <span>
                            <b>{{ $stats['crewHadir'] }}</b> crew sudah presensi, <b>{{ $stats['crewBelum'] }}</b> belum.
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="c-grid-bottom">
            <!-- Jadwal 7 Hari Mendatang -->
            <div class="c-card">
                <div class="c-card-header-icon" style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="title" style="font-size: 14px; font-weight: 600;">
                            <i class="ph ph-calendar-blank" style="vertical-align: middle; margin-right: 6px; color: var(--accent-gold);"></i> 
                            {{ $scheduleFilter === 'today' ? 'Jadwal Hari Ini' : 'Jadwal 7 Hari Mendatang' }}
                        </div>
                        <span class="c-badge dp" style="font-size: 10px; padding: 2px 7px;">
                            {{ $this->upcoming_orders->count() }} Acara
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="display: inline-flex; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 2px; font-size: 11px;">
                            <button type="button" 
                                wire:click="setScheduleFilter('7_days')"
                                style="border: none; background: {{ $scheduleFilter === '7_days' ? 'var(--accent-gold)' : 'transparent' }}; color: {{ $scheduleFilter === '7_days' ? '#000' : '#9ca3af' }}; padding: 3px 8px; border-radius: 4px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                7 Hari
                            </button>
                            <button type="button" 
                                wire:click="setScheduleFilter('today')"
                                style="border: none; background: {{ $scheduleFilter === 'today' ? 'var(--accent-gold)' : 'transparent' }}; color: {{ $scheduleFilter === 'today' ? '#000' : '#9ca3af' }}; padding: 3px 8px; border-radius: 4px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                Hari Ini
                            </button>
                        </div>
                        <a href="{{ url('/kalender') }}" style="font-size: 11px; color: var(--accent-gold); text-decoration: none; font-weight: 500;">
                            Kalender Lengkap &rarr;
                        </a>
                    </div>
                </div>

                @if($this->upcoming_orders->count() > 0)
                    <div class="c-orders-list" style="margin-top: 8px; max-height: 380px; overflow-y: auto;">
                        @foreach($this->upcoming_orders as $tOrder)
                            @php
                                $eventDate = \Carbon\Carbon::parse($tOrder->tanggal);
                                $isToday = $eventDate->isToday();
                                $isTomorrow = $eventDate->isTomorrow();
                                $badgeClass = match($tOrder->status) {
                                    'selesai' => 'selesai',
                                    'dp dibayar' => 'dp',
                                    'proses' => 'proses',
                                    'dibatalkan' => 'batal',
                                    default => 'masuk',
                                };
                            @endphp
                            <a href="{{ url('/orders/' . $tOrder->id) }}" style="text-decoration: none; color: inherit;">
                                <div class="c-order-item" style="padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1;">
                                        <!-- Date Badge -->
                                        <div style="min-width: 54px; text-align: center; padding: 4px 6px; border-radius: 6px; background: {{ $isToday ? 'rgba(207, 162, 75, 0.2)' : ($isTomorrow ? 'rgba(56, 189, 248, 0.15)' : 'rgba(255, 255, 255, 0.05)') }}; border: 1px solid {{ $isToday ? 'rgba(207, 162, 75, 0.4)' : ($isTomorrow ? 'rgba(56, 189, 248, 0.3)' : 'rgba(255, 255, 255, 0.1)') }};">
                                            @if($isToday)
                                                <div style="font-size: 9px; font-weight: 700; color: var(--accent-gold); text-transform: uppercase;">HARI INI</div>
                                                <div style="font-size: 13px; font-weight: 700; color: #fff;">{{ $eventDate->format('d M') }}</div>
                                            @elseif($isTomorrow)
                                                <div style="font-size: 9px; font-weight: 700; color: #38bdf8; text-transform: uppercase;">BESOK</div>
                                                <div style="font-size: 13px; font-weight: 700; color: #fff;">{{ $eventDate->format('d M') }}</div>
                                            @else
                                                <div style="font-size: 9px; font-weight: 600; color: #9ca3af; text-transform: uppercase;">{{ $eventDate->translatedFormat('D') }}</div>
                                                <div style="font-size: 13px; font-weight: 700; color: #fff;">{{ $eventDate->format('d M') }}</div>
                                            @endif
                                        </div>

                                        <div class="c-order-info" style="min-width: 0; flex: 1;">
                                            <h4 style="margin: 0; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $tOrder->nama_acara }} 
                                                <span style="font-size: 11px; opacity: 0.7;">{{ $tOrder->no_order }}</span>
                                            </h4>
                                            <p style="margin: 3px 0 0 0; font-size: 11px; color: var(--text-secondary, #9ca3af); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <i class="ph ph-clock" style="vertical-align: middle;"></i> {{ $tOrder->jam ? substr($tOrder->jam, 0, 5) . ' WIB' : 'Waktu TBA' }}
                                                &bull; <i class="ph ph-map-pin" style="vertical-align: middle;"></i> {{ $tOrder->venue ?? 'Venue belum diisi' }}
                                                @if($tOrder->customer)
                                                    &bull; <i class="ph ph-user" style="vertical-align: middle;"></i> {{ $tOrder->customer->nama }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="c-badge {{ $badgeClass }}" style="font-size: 9px; white-space: nowrap;">
                                        {{ strtoupper($tOrder->status) }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="c-schedule-empty" style="flex-direction: column; gap: 8px; padding: 28px 16px;">
                        <i class="ph ph-calendar-x" style="font-size: 28px; color: #6b7280;"></i>
                        <div style="color: #9ca3af; font-size: 13px;">
                            Tidak ada jadwal acara dalam {{ $scheduleFilter === 'today' ? 'hari ini' : '7 hari mendatang' }}.
                        </div>
                        <a href="{{ url('/kalender') }}" class="c-badge dp" style="text-decoration: none; padding: 6px 14px; font-size: 11px;">
                            Buka Kalender Jadwal
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pesanan Terbaru (Data Real dari Database) -->
            <div class="c-card">
                <div class="c-card-header-icon" style="margin-bottom: 16px;">
                    <div class="title" style="font-size: 14px;">
                        <i class="ph ph-package" style="vertical-align: middle; margin-right: 6px;"></i> 
                        Pesanan Terbaru
                    </div>
                    <a href="{{ url('/orders') }}" style="font-size: 11px; color: var(--accent-gold); text-decoration: none; font-weight: 500;">
                        Lihat Semua &rarr;
                    </a>
                </div>
                
                <div class="c-orders-list">
                    @forelse($recentOrders as $rOrder)
                        @php
                            $badgeClass = match($rOrder->status) {
                                'selesai' => 'selesai',
                                'dp dibayar' => 'dp',
                                'proses' => 'proses',
                                'dibatalkan' => 'batal',
                                default => 'masuk',
                            };
                            $clientDisplay = $rOrder->customer->nama ?? ($rOrder->nama_acara ?? 'Pelanggan');
                            $dateDisplay = $rOrder->tanggal ? \Carbon\Carbon::parse($rOrder->tanggal)->translatedFormat('d M Y') : '-';
                        @endphp
                        <a href="{{ url('/orders/' . $rOrder->id) }}" style="text-decoration: none; color: inherit;">
                            <div class="c-order-item">
                                <div class="c-order-info">
                                    <h4>{{ $rOrder->nama_acara }} <span>{{ $rOrder->no_order }}</span></h4>
                                    <p>{{ $clientDisplay }} &middot; {{ $dateDisplay }}</p>
                                </div>
                                <div class="c-badge {{ $badgeClass }}">{{ strtoupper($rOrder->status) }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="c-schedule-empty" style="flex-direction: column; gap: 8px;">
                            <div>Belum ada pesanan terdaftar.</div>
                            <a href="{{ \App\Filament\Resources\OrderResource::getUrl('create') }}" class="c-badge dp" style="text-decoration: none; padding: 6px 12px;">
                                + Buat Pesanan Baru
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

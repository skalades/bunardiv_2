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
        
        /* Override filament main bg for this page */
        main.fi-main { background-color: #0f0f11 !important; }
        .fi-topbar { background-color: #0f0f11 !important; border-bottom-color: #262626 !important; }
        .fi-sidebar { background-color: #0f0f11 !important; border-right-color: #262626 !important; }
        .fi-sidebar-item-active { background-color: rgba(207, 162, 75, 0.1) !important; color: #cfa24b !important; }
        .fi-header-heading { display: none !important; } /* Hide default Dashboard heading */

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
        
        .c-badge { padding: 6px 14px; border-radius: 100px; font-size: 10px; font-weight: 600; letter-spacing: 0.5px; }
        .c-badge.masuk { background-color: rgba(255,255,255,0.08); color: var(--text-primary); }
        .c-badge.selesai { background-color: rgba(62, 224, 138, 0.1); color: var(--success-green); border: 1px solid rgba(62, 224, 138, 0.15); }
        .c-badge.dp { background-color: rgba(207, 162, 75, 0.1); color: var(--accent-gold); border: 1px solid rgba(207, 162, 75, 0.15); }
    </style>
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <div class="custom-dashboard">
        <header class="c-header">
            <div class="c-header-subtitle">DASHBOARD OPERASIONAL</div>
            <h1 class="c-header-title font-serif">Selamat datang kembali.</h1>
        </header>

        <!-- Top Cards -->
        <div class="c-grid-cards">
            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">PESANAN HARI INI</div>
                    <div class="c-card-icon"><i class="ph ph-bag-check"></i></div>
                </div>
                <div class="c-card-value">0</div>
                <div class="c-card-subtitle">3 menunggu diproses</div>
            </div>
            
            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">TOTAL PEMASUKAN</div>
                    <div class="c-card-icon"><i class="ph ph-trend-up"></i></div>
                </div>
                <div class="c-card-value gold">Rp 46.250.000</div>
                <div class="c-card-subtitle">Semua pembayaran masuk</div>
            </div>

            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">PENGELUARAN</div>
                    <div class="c-card-icon"><i class="ph ph-wallet"></i></div>
                </div>
                <div class="c-card-value gold">Rp 2.950.000</div>
                <div class="c-card-subtitle">Operasional & gaji</div>
            </div>

            <div class="c-card">
                <div class="c-card-header">
                    <div class="c-card-title">PIUTANG PELANGGAN</div>
                    <div class="c-card-icon"><i class="ph ph-credit-card"></i></div>
                </div>
                <div class="c-card-value gold">Rp 25.000.000</div>
                <div class="c-card-subtitle">1 invoice belum lunas</div>
            </div>
        </div>

        <!-- Middle Section -->
        <div class="c-grid-middle">
            <div class="c-card c-profit-card">
                <div class="c-card-header-icon">
                    <div class="title">Estimasi Profit Bulan Ini</div>
                    <i class="ph ph-sparkle"></i>
                </div>
                <div class="c-profit-amount">Rp 43.300.000</div>
                
                <div class="c-profit-stats">
                    <div class="c-stat-item">
                        <div class="c-stat-label">PEMASUKAN</div>
                        <div class="c-stat-value gold">Rp 46.250.000</div>
                    </div>
                    <div class="c-stat-item">
                        <div class="c-stat-label">PENGELUARAN</div>
                        <div class="c-stat-value pink">- Rp 2.950.000</div>
                    </div>
                    <div class="c-stat-item">
                        <div class="c-stat-label">PESANAN SELESAI</div>
                        <div class="c-stat-value">1</div>
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
                        <div class="c-dot red"></div>
                        <span><b>1</b> invoice belum lunas.</span>
                    </li>
                    <li class="c-notification-item">
                        <div class="c-dot gold"></div>
                        <span><b>0</b> jadwal hari ini.</span>
                    </li>
                    <li class="c-notification-item">
                        <div class="c-dot green"></div>
                        <span><b>3</b> crew sudah presensi, 3 belum.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="c-grid-bottom">
            <div class="c-card">
                <div class="c-card-header-icon" style="margin-bottom: 8px;">
                    <div class="title" style="font-size: 14px;">
                        <i class="ph ph-calendar-blank" style="vertical-align: middle; margin-right: 6px;"></i> 
                        Jadwal Hari Ini
                    </div>
                </div>
                <div class="c-schedule-empty">
                    Tidak ada jadwal hari ini.
                </div>
            </div>

            <div class="c-card">
                <div class="c-card-header-icon" style="margin-bottom: 16px;">
                    <div class="title" style="font-size: 14px;">
                        <i class="ph ph-package" style="vertical-align: middle; margin-right: 6px;"></i> 
                        Pesanan Terbaru
                    </div>
                </div>
                
                <div class="c-orders-list">
                    <div class="c-order-item">
                        <div class="c-order-info">
                            <h4>TEST Event <span>ORD-2026-0005</span></h4>
                            <p>TEST_Customer &middot; 01 Mar 2026</p>
                        </div>
                        <div class="c-badge masuk">PESANAN MASUK</div>
                    </div>
                    <div class="c-order-item">
                        <div class="c-order-info">
                            <h4>Test2 <span>ORD-2026-0004</span></h4>
                            <p>C &middot; 31 Des 2026</p>
                        </div>
                        <div class="c-badge masuk">PESANAN MASUK</div>
                    </div>
                    <div class="c-order-item">
                        <div class="c-order-info">
                            <h4>Test Event <span>ORD-2026-0003</span></h4>
                            <p>TestCust &middot; 31 Des 2026</p>
                        </div>
                        <div class="c-badge masuk">PESANAN MASUK</div>
                    </div>
                    <div class="c-order-item">
                        <div class="c-order-info">
                            <h4>Festival Bunga <span>ORD-2026-0002</span></h4>
                            <p>NADYA &middot; 04 Des 2026</p>
                        </div>
                        <div class="c-badge selesai">SELESAI</div>
                    </div>
                    <div class="c-order-item">
                        <div class="c-order-info">
                            <h4>Wedding Rina & Fajar <span>ORD-2026-0001</span></h4>
                            <p>Rina & Fajar &middot; 01 Okt 2026</p>
                        </div>
                        <div class="c-badge dp">DP DIBAYAR</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

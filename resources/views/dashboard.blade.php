<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - N7 Decoration & Catering</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- Phosphor Icons for elegant minimalist icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">
                <i class="ph ph-crown"></i>
            </div>
            <div class="brand-text">
                N7 Decoration
                <span>& CATERING BU NARDI</span>
            </div>
        </div>

        <div class="menu-group">
            <div class="menu-title">OPERASIONAL</div>
            <ul class="menu-list">
                <li class="menu-item"><a href="#" class="menu-link active"><i class="ph ph-squares-four"></i> Dashboard</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-calendar-check"></i> Pesanan</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-calendar"></i> Kalender</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-truck"></i> Surat Jalan</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-title">KEUANGAN</div>
            <ul class="menu-list">
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-receipt"></i> Invoice</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-wallet"></i> Pembayaran</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-money"></i> Pengeluaran</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-clock-counter-clockwise"></i> Penggajian</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-title">SDM</div>
            <ul class="menu-list">
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-users"></i> Data Crew</a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-clock-user"></i> Presensi & Piket</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-title">INVENTORY</div>
            <ul class="menu-list">
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-package"></i> Barang</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-title">LAPORAN</div>
            <ul class="menu-list">
                <li class="menu-item"><a href="#" class="menu-link"><i class="ph ph-chart-line-up"></i> Laporan</a></li>
            </ul>
        </div>

        <div class="user-profile">
            <div class="user-info-wrapper">
                <div class="user-avatar">D</div>
                <div class="user-info">
                    <div class="user-name">Daffa (Owner)</div>
                    <div class="user-role">SUPER ADMIN</div>
                </div>
            </div>
            <button class="logout-btn"><i class="ph ph-sign-out"></i></button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div class="header-subtitle">DASHBOARD OPERASIONAL</div>
            <h1 class="header-title font-serif text-gold">Selamat datang kembali.</h1>
        </header>

        <!-- Top Cards -->
        <div class="grid-cards">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">PESANAN HARI INI</div>
                    <div class="card-icon darker"><i class="ph ph-bag-check"></i></div>
                </div>
                <div class="card-value">0</div>
                <div class="card-subtitle">3 menunggu diproses</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-title">TOTAL PEMASUKAN</div>
                    <div class="card-icon"><i class="ph ph-trend-up"></i></div>
                </div>
                <div class="card-value gold">Rp 46.250.000</div>
                <div class="card-subtitle">Semua pembayaran masuk</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">PENGELUARAN</div>
                    <div class="card-icon darker"><i class="ph ph-wallet"></i></div>
                </div>
                <div class="card-value gold">Rp 2.950.000</div>
                <div class="card-subtitle">Operasional & gaji</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">PIUTANG PELANGGAN</div>
                    <div class="card-icon darker"><i class="ph ph-credit-card"></i></div>
                </div>
                <div class="card-value gold">Rp 25.000.000</div>
                <div class="card-subtitle">1 invoice belum lunas</div>
            </div>
        </div>

        <!-- Middle Section -->
        <div class="grid-middle">
            <div class="card profit-card">
                <div class="card-header-icon">
                    <div class="title">Estimasi Profit Bulan Ini</div>
                    <i class="ph ph-sparkle"></i>
                </div>
                <div class="profit-amount">Rp 43.300.000</div>
                
                <div class="profit-stats">
                    <div class="stat-item">
                        <div class="stat-label">PEMASUKAN</div>
                        <div class="stat-value gold">Rp 46.250.000</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">PENGELUARAN</div>
                        <div class="stat-value pink">- Rp 2.950.000</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">PESANAN SELESAI</div>
                        <div class="stat-value">1</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header-icon" style="margin-bottom: 12px;">
                    <div class="title" style="color: var(--text-primary);">Notifikasi</div>
                    <i class="ph ph-warning" style="color: var(--accent-gold);"></i>
                </div>
                <ul class="notification-list">
                    <li class="notification-item">
                        <div class="dot red"></div>
                        <span><b>1</b> invoice belum lunas.</span>
                    </li>
                    <li class="notification-item">
                        <div class="dot gold"></div>
                        <span><b>0</b> jadwal hari ini.</span>
                    </li>
                    <li class="notification-item">
                        <div class="dot green"></div>
                        <span><b>3</b> crew sudah presensi, 3 belum.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="grid-bottom">
            <div class="card">
                <div class="card-header-icon" style="margin-bottom: 8px;">
                    <div class="title" style="color: var(--accent-gold); font-size: 13px;">
                        <i class="ph ph-calendar-blank" style="vertical-align: middle; margin-right: 6px;"></i> 
                        Jadwal Hari Ini
                    </div>
                </div>
                <div class="schedule-empty">
                    Tidak ada jadwal hari ini.
                </div>
            </div>

            <div class="card">
                <div class="card-header-icon" style="margin-bottom: 8px;">
                    <div class="title" style="color: var(--accent-gold); font-size: 13px;">
                        <i class="ph ph-package" style="vertical-align: middle; margin-right: 6px;"></i> 
                        Pesanan Terbaru
                    </div>
                </div>
                
                <div class="orders-list">
                    <div class="order-item">
                        <div class="order-info">
                            <h4>TEST Event <span>ORD-2026-0005</span></h4>
                            <p>TEST_Customer &middot; 01 Mar 2026</p>
                        </div>
                        <div class="badge masuk">PESANAN MASUK</div>
                    </div>
                    <div class="order-item">
                        <div class="order-info">
                            <h4>Test2 <span>ORD-2026-0004</span></h4>
                            <p>C &middot; 31 Des 2026</p>
                        </div>
                        <div class="badge masuk">PESANAN MASUK</div>
                    </div>
                    <div class="order-item">
                        <div class="order-info">
                            <h4>Test Event <span>ORD-2026-0003</span></h4>
                            <p>TestCust &middot; 31 Des 2026</p>
                        </div>
                        <div class="badge masuk">PESANAN MASUK</div>
                    </div>
                    <div class="order-item">
                        <div class="order-info">
                            <h4>Festival Bunga <span>ORD-2026-0002</span></h4>
                            <p>NADYA &middot; 04 Des 2026</p>
                        </div>
                        <div class="badge selesai">SELESAI</div>
                    </div>
                    <div class="order-item">
                        <div class="order-info">
                            <h4>Wedding Rina & Fajar <span>ORD-2026-0001</span></h4>
                            <p>Rina & Fajar &middot; 01 Okt 2026</p>
                        </div>
                        <div class="badge dp">DP DIBAYAR</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

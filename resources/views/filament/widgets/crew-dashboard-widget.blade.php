<x-filament-widgets::widget class="crew-mobile-dashboard">
    <div class="crew-dashboard-container">

        <!-- Top Profile & Status Card -->
        <div class="crew-card crew-header-card">
            <div class="crew-header-top">
                <div class="crew-avatar">
                    <i class="ph-bold ph-user"></i>
                </div>
                <div class="crew-header-info">
                    <span class="crew-pill-role">{{ ucfirst($profile->tipe ?? 'Crew') }}</span>
                    <h2 class="crew-name">{{ $user->name }}</h2>
                    <div class="crew-meta">
                        <span>{{ $profile->jabatan ?? 'Crew Lapangan' }}</span>
                        @if($profile?->nik)
                            <span>•</span>
                            <span>NIK: {{ $profile->nik }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Date badge -->
            <div class="crew-header-date">
                <i class="ph ph-calendar"></i> {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <!-- Personal Summary Stats -->
        <div class="crew-stats-grid">
            <div class="crew-stat-card">
                <span class="crew-stat-label">Event Hadir</span>
                <span class="crew-stat-value gold">{{ $eventHadirBulanIni }} <small>SPK</small></span>
                <span class="crew-stat-sub">Bulan Ini</span>
            </div>

            @if(($profile->tipe ?? '') === 'bulanan')
                <div class="crew-stat-card">
                    <span class="crew-stat-label">Hari Hadir</span>
                    <span class="crew-stat-value green">{{ $hariHadirBulanIni }} <small>Hari</small></span>
                    <span class="crew-stat-sub">Presensi Kantor</span>
                </div>
            @endif

            <div class="crew-stat-card">
                <span class="crew-stat-label">Sisa Kasbon</span>
                <span class="crew-stat-value {{ $kasbonAktif > 0 ? 'red' : 'green' }}">
                    Rp {{ number_format($kasbonAktif, 0, ',', '.') }}
                </span>
                <span class="crew-stat-sub">{{ $kasbonAktif > 0 ? 'Cicilan Berjalan' : 'Tidak Ada Hutang' }}</span>
            </div>
        </div>

        <!-- Presensi Harian Kantor (Khusus Karyawan Bulanan) -->
        @if(($profile->tipe ?? '') === 'bulanan')
            <div class="crew-card crew-daily-card">
                <div class="crew-card-title">
                    <i class="ph-bold ph-clock"></i> Presensi Kantor Hari Ini
                </div>

                @if($todayAttendance)
                    <div class="crew-attendance-done">
                        <i class="ph-bold ph-check-circle"></i>
                        <div>
                            <strong>Sudah Hadir Hari Ini</strong>
                            <div class="crew-time-sub">Dicatat pukul {{ $todayAttendance->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>
                @else
                    <button
                        type="button"
                        wire:click="checkInDaily('hadir')"
                        wire:loading.attr="disabled"
                        class="crew-btn-primary crew-btn-large"
                    >
                        <i class="ph-bold ph-fingerprint"></i>
                        <span>Absen Masuk Kantor</span>
                    </button>
                @endif
            </div>
        @endif

        <!-- Daftar Penugasan Lapangan & SPK (Self-Absensi) -->
        <div class="crew-card">
            <div class="crew-section-header">
                <div>
                    <h3 class="crew-section-title">
                        <i class="ph-bold ph-clipboard-text"></i> Tugas Lapangan Saya
                    </h3>
                    <p class="crew-section-desc">Absen hadir saat Anda tiba di lokasi acara</p>
                </div>
                <a href="{{ url('/absensi-saya') }}" class="crew-link-all">
                    Lihat Semua <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            @if($assignments->isEmpty())
                <div class="crew-empty-box">
                    <i class="ph ph-calendar-blank"></i>
                    <h4>Belum Ada Penugasan</h4>
                    <p>Anda belum memiliki jadwal SPK aktif saat ini.</p>
                </div>
            @else
                <div class="crew-task-list">
                    @foreach($assignments as $item)
                        @php
                            $wo = $item->workOrder;
                            $order = $wo?->order;
                        @endphp
                        <div class="crew-task-item {{ $item->hadir ? 'is-present' : '' }}">
                            <div class="crew-task-header">
                                <span class="crew-spk-badge">{{ $wo?->spk_number ?? 'SPK' }}</span>
                                <span class="crew-role-badge">{{ $item->peran }}</span>
                                @if($item->upah_dibayar)
                                    <span class="crew-wage-badge">Rp {{ number_format($item->upah_dibayar, 0, ',', '.') }}</span>
                                @endif
                            </div>

                            <h4 class="crew-event-title">{{ $order?->nama_acara ?? 'Event Persiapan' }}</h4>

                            <div class="crew-event-details">
                                <div class="crew-detail-row">
                                    <i class="ph ph-calendar"></i>
                                    <span>{{ $wo?->tanggal_persiapan ? \Carbon\Carbon::parse($wo->tanggal_persiapan)->format('d M Y') : '-' }}</span>
                                    @if($order?->jam)
                                        <span>• {{ $order->jam }}</span>
                                    @endif
                                </div>
                                @if($order?->venue)
                                    <div class="crew-detail-row">
                                        <i class="ph ph-map-pin"></i>
                                        <span>{{ $order->venue }}</span>
                                    </div>
                                @endif
                                @if($order?->customer)
                                    <div class="crew-detail-row">
                                        <i class="ph ph-user"></i>
                                        <span>Customer: {{ $order->customer->nama }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button / Attendance Status -->
                            <div class="crew-action-area">
                                @if($item->hadir)
                                    <div class="crew-badge-attended">
                                        <i class="ph-bold ph-check-circle"></i>
                                        <div>
                                            <span>SUDAH HADIR DI LOKASI</span>
                                            @if($item->waktu_absen)
                                                <small>{{ $item->waktu_absen->format('d M Y, H:i') }} WIB</small>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <button
                                        type="button"
                                        wire:click="checkInEvent({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="crew-btn-checkin"
                                    >
                                        <i class="ph-bold ph-map-pin"></i>
                                        <span>ABSEN HADIR SEKARANG</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Mobile-1st Scoped CSS -->
    <style>
        .crew-mobile-dashboard {
            width: 100% !important;
            box-shadow: none !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }

        .crew-dashboard-container {
            max-width: 680px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            font-family: 'Inter', sans-serif;
        }

        .crew-card {
            background: #15161d;
            border: 1px solid #20232e;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Header Profile */
        .crew-header-card {
            background: linear-gradient(145deg, #181a24 0%, #121319 100%);
            border-color: #262938;
        }

        .crew-header-top {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .crew-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(207, 162, 75, 0.12);
            border: 2px solid rgba(207, 162, 75, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cfa24b;
            font-size: 26px;
            flex-shrink: 0;
        }

        .crew-header-info {
            flex: 1;
        }

        .crew-pill-role {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            background: rgba(207, 162, 75, 0.15);
            color: #cfa24b;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .crew-name {
            font-size: 20px;
            font-weight: 700;
            color: #f2f3f7;
            margin: 0;
            line-height: 1.2;
        }

        .crew-meta {
            font-size: 12px;
            color: #8b92a4;
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 3px;
        }

        .crew-header-date {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 12px;
            color: #a0a6b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Stats Grid */
        .crew-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
        }

        .crew-stat-card {
            background: #15161d;
            border: 1px solid #20232e;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .crew-stat-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #71788e;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .crew-stat-value {
            font-size: 18px;
            font-weight: 700;
            font-family: monospace;
            line-height: 1.2;
        }

        .crew-stat-value.gold { color: #cfa24b; }
        .crew-stat-value.green { color: #2ecc71; }
        .crew-stat-value.red { color: #e74c3c; }

        .crew-stat-value small {
            font-size: 11px;
            font-weight: 400;
            color: #8b92a4;
        }

        .crew-stat-sub {
            font-size: 10px;
            color: #5e6475;
        }

        /* Daily Card */
        .crew-daily-card {
            border-left: 3px solid #cfa24b;
        }

        .crew-card-title {
            font-size: 13px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .crew-attendance-done {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.25);
            padding: 12px 16px;
            border-radius: 10px;
            color: #2ecc71;
            font-size: 13px;
        }

        .crew-attendance-done i {
            font-size: 24px;
        }

        .crew-time-sub {
            font-size: 11px;
            color: #8b92a4;
            font-weight: 400;
            margin-top: 1px;
        }

        /* Buttons */
        .crew-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(46, 204, 113, 0.35);
            transition: all 0.15s ease;
            width: 100%;
        }

        .crew-btn-primary:active {
            transform: scale(0.98);
        }

        /* Task List */
        .crew-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .crew-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #f1f5f9;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .crew-section-desc {
            font-size: 12px;
            color: #71788e;
            margin: 2px 0 0 0;
        }

        .crew-link-all {
            font-size: 12px;
            color: #cfa24b;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .crew-task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .crew-task-item {
            background: #0f1015;
            border: 1px solid #20232e;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: border-color 0.2s ease;
        }

        .crew-task-item.is-present {
            border-color: rgba(46, 204, 113, 0.3);
            background: rgba(46, 204, 113, 0.02);
        }

        .crew-task-header {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .crew-spk-badge {
            font-family: monospace;
            font-size: 11px;
            background: rgba(207, 162, 75, 0.12);
            color: #cfa24b;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
        }

        .crew-role-badge {
            font-size: 11px;
            background: #1c202d;
            color: #cbd5e1;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 600;
        }

        .crew-wage-badge {
            font-size: 11px;
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            margin-left: auto;
        }

        .crew-event-title {
            font-size: 16px;
            font-weight: 700;
            color: #f1f5f9;
            margin: 0;
            line-height: 1.3;
        }

        .crew-event-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .crew-detail-row {
            font-size: 12px;
            color: #8b92a4;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .crew-detail-row i {
            color: #cfa24b;
            font-size: 13px;
        }

        .crew-action-area {
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .crew-btn-checkin {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #d8ab54, #b78a36);
            color: #0c0d10;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(207, 162, 75, 0.35);
            transition: all 0.15s ease;
        }

        .crew-btn-checkin:active {
            transform: scale(0.98);
        }

        .crew-badge-attended {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.25);
            padding: 10px 14px;
            border-radius: 8px;
            color: #2ecc71;
            font-weight: 700;
            font-size: 12px;
        }

        .crew-badge-attended i {
            font-size: 20px;
        }

        .crew-badge-attended small {
            display: block;
            font-size: 10px;
            color: #8b92a4;
            font-weight: 400;
            margin-top: 1px;
        }

        .crew-empty-box {
            text-align: center;
            padding: 30px 16px;
            color: #71788e;
            background: #0f1015;
            border-radius: 12px;
            border: 1px dashed #20232e;
        }

        .crew-empty-box i {
            font-size: 32px;
            color: #3b4254;
            margin-bottom: 8px;
        }

        .crew-empty-box h4 {
            font-size: 14px;
            font-weight: 600;
            color: #cbd5e1;
            margin: 0 0 2px 0;
        }

        .crew-empty-box p {
            font-size: 12px;
            margin: 0;
        }
    </style>
</x-filament-widgets::widget>

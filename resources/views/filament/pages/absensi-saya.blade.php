<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px; font-family: 'Inter', sans-serif;">

        <!-- Header Profile & Welcome Banner -->
        <div style="background: #15161d; border: 1px solid #20232e; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 18px;">
                <div style="width: 58px; height: 58px; border-radius: 14px; background: rgba(207, 162, 75, 0.12); border: 1px solid rgba(207, 162, 75, 0.3); display: flex; align-items: center; justify-content: center; color: #cfa24b; font-size: 30px;">
                    <x-heroicon-o-user-circle style="width: 32px; height: 32px;" />
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #cfa24b; font-weight: 700; margin-bottom: 4px;">
                        PORTAL KARYAWAN & CREW LAPANGAN
                    </div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #f2f3f7; margin: 0; letter-spacing: -0.5px;">
                        {{ $user->name }}
                    </h2>
                    <div style="display: flex; gap: 12px; align-items: center; margin-top: 6px; font-size: 13px; color: #8b92a4; flex-wrap: wrap;">
                        <span><strong style="color: #cbd5e1;">NIK:</strong> {{ $profile->nik ?? '-' }}</span>
                        <span>•</span>
                        <span><strong style="color: #cbd5e1;">Jabatan:</strong> {{ $profile->jabatan ?? 'Crew' }}</span>
                        <span>•</span>
                        <span style="background: {{ ($profile->tipe ?? 'harian') === 'bulanan' ? 'rgba(46, 204, 113, 0.15)' : 'rgba(230, 126, 34, 0.15)' }}; color: {{ ($profile->tipe ?? 'harian') === 'bulanan' ? '#2ecc71' : '#e67e22' }}; padding: 2px 8px; border-radius: 6px; font-weight: 600; font-size: 11px; text-transform: uppercase;">
                            {{ ucfirst($profile->tipe ?? 'harian') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Current Date & Clock -->
            <div style="background: #0f1015; border: 1px solid #1e2029; border-radius: 12px; padding: 12px 20px; text-align: right;">
                <div style="font-size: 11px; text-transform: uppercase; color: #71788e; font-weight: 600; letter-spacing: 1px;">HARI INI</div>
                <div style="font-size: 15px; font-weight: 700; color: #f1f5f9; margin-top: 2px;">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>

        <!-- Mini Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div style="background: #15161d; border: 1px solid #20232e; border-radius: 12px; padding: 18px 20px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #71788e; font-weight: 600; letter-spacing: 1px;">Event Dihadiri Bulan Ini</div>
                <div style="font-size: 26px; font-weight: 700; color: #cfa24b; margin-top: 6px; font-family: monospace;">
                    {{ $eventHadirBulanIni }} <span style="font-size: 14px; font-weight: 400; color: #8b92a4;">SPK</span>
                </div>
            </div>

            @if(($profile->tipe ?? '') === 'bulanan')
                <div style="background: #15161d; border: 1px solid #20232e; border-radius: 12px; padding: 18px 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #71788e; font-weight: 600; letter-spacing: 1px;">Kehadiran Harian Kantor</div>
                    <div style="font-size: 26px; font-weight: 700; color: #2ecc71; margin-top: 6px; font-family: monospace;">
                        {{ $hariHadirBulanIni }} <span style="font-size: 14px; font-weight: 400; color: #8b92a4;">Hari</span>
                    </div>
                </div>
            @endif

            <div style="background: #15161d; border: 1px solid #20232e; border-radius: 12px; padding: 18px 20px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #71788e; font-weight: 600; letter-spacing: 1px;">Sisa Kasbon Berjalan</div>
                <div style="font-size: 22px; font-weight: 700; color: {{ $kasbonAktif > 0 ? '#e74c3c' : '#2ecc71' }}; margin-top: 6px; font-family: monospace;">
                    Rp {{ number_format($kasbonAktif, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Section 1: Absensi Harian (Bagi Karyawan Bulanan) -->
        @if(($profile->tipe ?? '') === 'bulanan')
            <div style="background: #15161d; border: 1px solid #20232e; border-radius: 16px; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #f1f5f9; margin: 0; display: flex; align-items: center; gap: 10px;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #cfa24b;"></span>
                            Absensi Harian Kantor
                        </h3>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #71788e;">Pencatatan presensi kerja kantor untuk hari ini.</p>
                    </div>

                    @if($todayAttendance)
                        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.3); color: #2ecc71; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px;">
                            <x-heroicon-o-check-circle style="width: 18px; height: 18px;" />
                            Sudah Absen: {{ ucfirst($todayAttendance->status) }}
                            <span style="font-size: 11px; color: #8b92a4;">({{ $todayAttendance->created_at->format('H:i') }} WIB)</span>
                        </div>
                    @endif
                </div>

                @if(! $todayAttendance)
                    <div style="background: #0f1015; border: 1px dashed #2a2e3d; border-radius: 12px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div>
                            <div style="font-weight: 600; color: #e2e8f0; font-size: 15px;">Anda belum melakukan absensi hari ini</div>
                            <div style="font-size: 13px; color: #71788e; margin-top: 2px;">Klik tombol di samping untuk mencatat kehadiran kantor Anda.</div>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button
                                type="button"
                                wire:click="checkInDaily('hadir')"
                                wire:loading.attr="disabled"
                                style="cursor: pointer; display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #2ecc71, #27ae60); color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 600; font-size: 14px; box-shadow: 0 2px 10px rgba(46, 204, 113, 0.3);"
                            >
                                <x-heroicon-o-finger-print style="width: 18px; height: 18px;" />
                                Absen Hadir Sekarang
                            </button>

                            <button
                                type="button"
                                wire:click="checkInDaily('izin', 'Izin tidak masuk kantor')"
                                wire:loading.attr="disabled"
                                style="cursor: pointer; display: inline-flex; align-items: center; gap: 8px; background: #20232e; color: #cbd5e1; border: 1px solid #2d3345; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px;"
                            >
                                Ajukan Izin
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Section 2: Penugasan Acara & SPK Lapangan (Self-Absensi Event) -->
        <div id="tugas" style="background: #15161d; border: 1px solid #20232e; border-radius: 16px; padding: 24px;">
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f1f5f9; margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #cfa24b;"></span>
                    Tugas Lapangan & SPK Saya
                </h3>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #71788e;">
                    Daftar acara / event yang ditugaskan kepada Anda. Klik <strong>"Absen Hadir"</strong> saat Anda tiba di lokasi acara.
                </p>
            </div>

            @if($assignments->isEmpty())
                <div style="background: #0f1015; border: 1px dashed #2a2e3d; border-radius: 12px; padding: 36px 20px; text-align: center; color: #71788e;">
                    <x-heroicon-o-clipboard-document-list style="width: 42px; height: 42px; margin: 0 auto 12px auto; color: #3b4254;" />
                    <div style="font-weight: 600; font-size: 15px; color: #cbd5e1;">Belum Ada Penugasan Aktif</div>
                    <div style="font-size: 13px; margin-top: 4px;">Anda belum dimasukkan ke dalam tim persiapan atau SPK event apapun saat ini.</div>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($assignments as $item)
                        @php
                            $wo = $item->workOrder;
                            $order = $wo?->order;
                        @endphp
                        <div style="background: #0f1015; border: 1px solid {{ $item->hadir ? 'rgba(46, 204, 113, 0.25)' : '#20232e' }}; border-radius: 14px; padding: 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
                            
                            <!-- Left Info -->
                            <div style="flex: 1; min-width: 260px;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; flex-wrap: wrap;">
                                    <span style="font-family: monospace; font-size: 12px; background: rgba(207, 162, 75, 0.12); color: #cfa24b; border: 1px solid rgba(207, 162, 75, 0.25); padding: 2px 8px; border-radius: 6px; font-weight: 700;">
                                        {{ $wo?->spk_number ?? 'SPK' }}
                                    </span>
                                    <span style="font-size: 12px; background: #1c202d; color: #cbd5e1; padding: 2px 8px; border-radius: 6px; font-weight: 600;">
                                        Peran: {{ $item->peran }}
                                    </span>
                                    @if($item->upah_dibayar)
                                        <span style="font-size: 12px; background: rgba(46, 204, 113, 0.1); color: #2ecc71; padding: 2px 8px; border-radius: 6px; font-weight: 600;">
                                            Upah: Rp {{ number_format($item->upah_dibayar, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <h4 style="font-size: 17px; font-weight: 700; color: #f1f5f9; margin: 0 0 6px 0;">
                                    {{ $order?->nama_acara ?? 'Event Persiapan' }}
                                    @if($order?->customer)
                                        <span style="font-size: 13px; font-weight: 400; color: #8b92a4;">({{ $order->customer->nama }})</span>
                                    @endif
                                </h4>

                                <div style="display: flex; gap: 16px; font-size: 12px; color: #71788e; flex-wrap: wrap;">
                                    <span>📅 <strong>Tgl Persiapan:</strong> {{ $wo?->tanggal_persiapan ? \Carbon\Carbon::parse($wo->tanggal_persiapan)->format('d M Y') : '-' }}</span>
                                    @if($order?->jam)
                                        <span>⏰ <strong>Jam:</strong> {{ $order->jam }}</span>
                                    @endif
                                    @if($order?->venue)
                                        <span>📍 <strong>Venue:</strong> {{ $order->venue }}</span>
                                    @endif
                                </div>

                                @if($item->catatan_kehadiran)
                                    <div style="font-size: 11px; color: #8b92a4; margin-top: 6px; font-style: italic;">
                                        Catatan: {{ $item->catatan_kehadiran }}
                                    </div>
                                @endif
                            </div>

                            <!-- Right Action (Self Check-in Button / Status) -->
                            <div style="text-align: right;">
                                @if($item->hadir)
                                    <div style="display: inline-flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.35); color: #2ecc71; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px;">
                                            <x-heroicon-o-check-circle style="width: 18px; height: 18px;" />
                                            SUDAH HADIR
                                        </div>
                                        @if($item->waktu_absen)
                                            <span style="font-size: 11px; color: #71788e;">
                                                Absen: {{ $item->waktu_absen->format('d M Y H:i') }} WIB
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                                        <button
                                            type="button"
                                            wire:click="checkInEvent({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                            style="cursor: pointer; display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #d8ab54, #b78a36); color: #0c0d10; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; box-shadow: 0 2px 12px rgba(207, 162, 75, 0.3); transition: transform 0.1s ease;"
                                            onmouseover="this.style.transform='scale(1.02)'"
                                            onmouseout="this.style.transform='scale(1)'"
                                        >
                                            <x-heroicon-o-map-pin style="width: 18px; height: 18px;" />
                                            Absen Hadir di Lokasi
                                        </button>
                                        <span style="font-size: 11px; color: #e67e22; font-weight: 500;">
                                            *Klik saat Anda sudah tiba di lokasi
                                        </span>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>

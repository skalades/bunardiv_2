<x-filament-widgets::widget>
    <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Top Greeting Bar -->
        <div style="background: #15161d; border: 1px solid #20232e; border-radius: 14px; padding: 22px 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 18px;">
                <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(207, 162, 75, 0.12); border: 1px solid rgba(207, 162, 75, 0.25); display: flex; align-items: center; justify-content: center; color: #cfa24b; font-size: 26px;">
                    <x-heroicon-o-finger-print style="width: 28px; height: 28px;" />
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #cfa24b; font-weight: 700; margin-bottom: 4px;">
                        PORTAL CREW & PRESENSI MANDIRI
                    </div>
                    <h2 style="font-size: 20px; font-weight: 700; color: #f2f3f7; margin: 0;">
                        Halo, {{ $user->name }}!
                    </h2>
                    <p style="margin: 3px 0 0 0; color: #8b92a4; font-size: 13px;">
                        Silakan periksa tugas lapangan Anda di bawah ini dan lakukan absensi mandiri saat tiba di lokasi.
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <a href="{{ url('/absensi-saya') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; background: linear-gradient(135deg, #d8ab54, #b78a36); color: #0c0d10; font-weight: 700; font-size: 13px; text-decoration: none; box-shadow: 0 2px 10px rgba(207, 162, 75, 0.25);">
                    <x-heroicon-o-calendar-days style="width: 16px; height: 16px;" /> Buka Portal Absensi Lengkap
                </a>
            </div>
        </div>

        <!-- Quick Daily Check-in (Khusus Karyawan Bulanan) -->
        @if(($profile->tipe ?? '') === 'bulanan')
            <div style="background: #15161d; border: 1px solid #20232e; border-radius: 14px; padding: 18px 22px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: #cfa24b; font-weight: 700; letter-spacing: 1px;">PRESENSI KANTOR HARI INI</div>
                    <div style="font-size: 14px; font-weight: 600; color: #f1f5f9; margin-top: 2px;">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>

                @if($todayAttendance)
                    <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.3); color: #2ecc71; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px;">
                        <x-heroicon-o-check-circle style="width: 18px; height: 18px;" />
                        Sudah Absen: {{ ucfirst($todayAttendance->status) }} ({{ $todayAttendance->created_at->format('H:i') }} WIB)
                    </div>
                @else
                    <button
                        type="button"
                        wire:click="checkInDaily('hadir')"
                        wire:loading.attr="disabled"
                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #2ecc71, #27ae60); color: #fff; border: none; padding: 9px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; box-shadow: 0 2px 10px rgba(46, 204, 113, 0.3);"
                    >
                        <x-heroicon-o-finger-print style="width: 18px; height: 18px;" /> Absen Masuk Kantor
                    </button>
                @endif
            </div>
        @endif

        <!-- Penugasan SPK & Event (Self-Absensi Cepat) -->
        <div style="background: #15161d; border: 1px solid #20232e; border-radius: 14px; padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f1f5f9; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #cfa24b;"></span>
                    Tugas Lapangan & SPK Terbaru
                </h3>
                <span style="font-size: 12px; color: #71788e;">Klik tombol kuning untuk absen hadir di lokasi</span>
            </div>

            @if($assignments->isEmpty())
                <div style="text-align: center; padding: 24px; color: #71788e; font-size: 13px; background: #0f1015; border-radius: 10px; border: 1px dashed #20232e;">
                    Belum ada penugasan SPK aktif untuk Anda saat ini.
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($assignments as $item)
                        @php
                            $wo = $item->workOrder;
                            $order = $wo?->order;
                        @endphp
                        <div style="background: #0f1015; border: 1px solid {{ $item->hadir ? 'rgba(46, 204, 113, 0.2)' : '#20232e' }}; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                    <span style="font-family: monospace; font-size: 11px; background: rgba(207, 162, 75, 0.12); color: #cfa24b; padding: 2px 7px; border-radius: 5px; font-weight: 700;">
                                        {{ $wo?->spk_number ?? 'SPK' }}
                                    </span>
                                    <span style="font-size: 11px; background: #1c202d; color: #cbd5e1; padding: 2px 7px; border-radius: 5px; font-weight: 600;">
                                        Tugas: {{ $item->peran }}
                                    </span>
                                </div>
                                <div style="font-size: 15px; font-weight: 700; color: #f1f5f9;">
                                    {{ $order?->nama_acara ?? 'Event Persiapan' }}
                                    @if($order?->customer)
                                        <span style="font-size: 12px; font-weight: 400; color: #8b92a4;">({{ $order->customer->nama }})</span>
                                    @endif
                                </div>
                                <div style="font-size: 12px; color: #71788e; margin-top: 3px;">
                                    📅 {{ $wo?->tanggal_persiapan ? \Carbon\Carbon::parse($wo->tanggal_persiapan)->format('d M Y') : '-' }}
                                    @if($order?->venue) • 📍 {{ $order->venue }} @endif
                                </div>
                            </div>

                            <div>
                                @if($item->hadir)
                                    <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.3); color: #2ecc71; padding: 7px 14px; border-radius: 7px; font-weight: 700; font-size: 12px;">
                                        <x-heroicon-o-check-circle style="width: 16px; height: 16px;" />
                                        HADIR
                                    </div>
                                @else
                                    <button
                                        type="button"
                                        wire:click="checkInEvent({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #d8ab54, #b78a36); color: #0c0d10; border: none; padding: 8px 16px; border-radius: 7px; font-weight: 700; font-size: 12px; box-shadow: 0 2px 8px rgba(207, 162, 75, 0.3);"
                                    >
                                        <x-heroicon-o-map-pin style="width: 16px; height: 16px;" />
                                        Absen Hadir
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-filament-widgets::widget>

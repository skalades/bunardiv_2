<x-filament-widgets::widget>
    <div style="background: #15161d; border: 1px solid #20232e; border-radius: 14px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(207, 162, 75, 0.12); border: 1px solid rgba(207, 162, 75, 0.25); display: flex; align-items: center; justify-content: center; color: #cfa24b; font-size: 26px;">
                <i class="ph ph-user-circle"></i>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #cfa24b; font-weight: 700; margin-bottom: 4px;">PORTAL CREW OPERASIONAL</div>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 600; color: #f2f3f7; margin: 0;">Halo, {{ auth()->user()->name }}!</h2>
                <p style="margin: 4px 0 0 0; color: #8b92a4; font-size: 13px;">Anda login sebagai Crew. Silakan periksa jadwal persiapan dan daftar tugas hari ini.</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ url('/work-orders') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; background: linear-gradient(135deg, #d8ab54, #b78a36); color: #0c0d10; font-weight: 600; font-size: 13px; text-decoration: none; box-shadow: 0 2px 10px rgba(207, 162, 75, 0.25);">
                <i class="ph ph-clipboard-text" style="font-size: 16px;"></i> Lihat Surat Perintah Kerja (SPK)
            </a>
        </div>
    </div>
</x-filament-widgets::widget>

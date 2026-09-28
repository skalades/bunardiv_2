<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $workOrder->surat_jalan_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #27ae60; padding-bottom: 15px; margin-bottom: 25px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo { width: 30%; }
        .header-logo img { max-width: 160px; max-height: 80px; }
        .header-info { width: 70%; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #27ae60; margin-bottom: 5px; }
        .company-contact { font-size: 12px; color: #555; line-height: 1.4; }
        
        .doc-title-container { text-align: center; margin-bottom: 25px; }
        .doc-title { font-size: 20px; font-weight: bold; text-transform: uppercase; color: #27ae60; letter-spacing: 1px; border-bottom: 1px solid #333; display: inline-block; padding-bottom: 3px; margin-bottom: 5px; }
        .doc-number { font-size: 14px; color: #555; }
        
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { vertical-align: top; border: none; padding: 0; }
        .info-box { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 12px; height: 100%; }
        .info-box-title { font-size: 12px; font-weight: bold; color: #7f8c8d; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .info-row { margin-bottom: 5px; display: table; width: 100%; }
        .info-label { display: table-cell; width: 110px; font-weight: bold; font-size: 12px; color: #34495e; }
        .info-value { display: table-cell; font-size: 13px; }
        
        .section-title { font-size: 14px; font-weight: bold; color: #27ae60; margin-bottom: 5px; text-transform: uppercase; border-left: 4px solid #2ecc71; padding-left: 8px; }
        .section-subtitle { font-size: 11px; color: #7f8c8d; margin-bottom: 15px; font-style: italic; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; }
        .data-table th { background-color: #27ae60; color: #ffffff; font-weight: bold; text-align: left; font-size: 12px; text-transform: uppercase; }
        .data-table tbody tr:nth-child(even) { background-color: #f9fbf9; }
        
        .checklist-box { width: 20px; height: 20px; border: 1px solid #999; display: inline-block; margin: 0 auto; border-radius: 3px; }
        
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-table td { text-align: center; vertical-align: bottom; border: none; width: 33.33%; padding: 0 15px; }
        .signature-title { margin-bottom: 70px; font-weight: bold; font-size: 13px; color: #2c3e50; }
        .signature-line { border-bottom: 1px solid #333; margin-bottom: 5px; }
        .signature-name { font-size: 12px; color: #555; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($setting && $setting->company_logo)
                    <img src="{{ public_path('storage/'.$setting->company_logo) }}" alt="Logo">
                @endif
            </td>
            <td class="header-info">
                <div class="company-name">{{ $setting ? $setting->company_name : 'NAMA PERUSAHAAN' }}</div>
                <div class="company-contact">
                    {{ $setting ? $setting->company_address : 'Alamat Perusahaan' }}<br>
                    Telp: {{ $setting ? $setting->company_phone : '-' }} 
                    @if($setting && $setting->company_email) | Email: {{ $setting->company_email }} @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title-container">
        <div class="doc-title">Surat Jalan Pengiriman</div>
        <div class="doc-number">No. Dokumen: {{ $workOrder->surat_jalan_number }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 48%; padding-right: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Detail Pengiriman</div>
                    <div class="info-row"><span class="info-label">Tgl Kirim</span><span class="info-value">: {{ \Carbon\Carbon::parse($workOrder->tanggal_persiapan)->format('d F Y') }}</span></div>
                    <div class="info-row"><span class="info-label">PIC Pengirim</span><span class="info-value">: {{ $workOrder->pic ? $workOrder->pic->name : '-' }}</span></div>
                    <div class="info-row"><span class="info-label">No. Kendaraan</span><span class="info-value">: ...............................</span></div>
                </div>
            </td>
            <td style="width: 4%;">&nbsp;</td>
            <td style="width: 48%; padding-left: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Tujuan & Penerima</div>
                    <div class="info-row"><span class="info-label">Nama Klien</span><span class="info-value">: {{ $workOrder->order->customer ? $workOrder->order->customer->nama : '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Telepon</span><span class="info-value">: {{ $workOrder->order->customer ? $workOrder->order->customer->no_telp : '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Alamat / Venue</span><span class="info-value">: {{ $workOrder->order->venue ?? '-' }}</span></div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Muatan & Peralatan</div>
    <div class="section-subtitle">* Mohon periksa kelengkapan dan kesesuaian jumlah barang secara seksama.</div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="55%">Nama Barang / Peralatan</th>
                <th width="15%" class="text-center">Jumlah</th>
                <th width="15%" class="text-center">Satuan</th>
                <th width="10%" class="text-center">Cek</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrder->equipment as $index => $eq)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $eq->inventory ? $eq->inventory->nama : '-' }}</strong></td>
                <td class="text-center" style="font-weight: bold; font-size: 14px;">{{ $eq->jumlah }}</td>
                <td class="text-center">{{ $eq->inventory ? $eq->inventory->satuan : '-' }}</td>
                <td class="text-center"><div class="checklist-box"></div></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data peralatan logistik yang akan dikirim.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Dikeluarkan Oleh,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Bagian Gudang / Logistik</div>
            </td>
            <td>
                <div class="signature-title">Dikirim Oleh,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Driver / Kurir</div>
            </td>
            <td>
                <div class="signature-title">Diterima Oleh,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Klien / Perwakilan</div>
            </td>
        </tr>
    </table>
</body>
</html>

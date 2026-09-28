<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Kerja (SPK) - {{ $workOrder->spk_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #2c3e50; padding-bottom: 15px; margin-bottom: 25px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo { width: 30%; }
        .header-logo img { max-width: 160px; max-height: 80px; }
        .header-info { width: 70%; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #2c3e50; margin-bottom: 5px; }
        .company-contact { font-size: 12px; color: #555; line-height: 1.4; }
        
        .doc-title-container { text-align: center; margin-bottom: 25px; }
        .doc-title { font-size: 20px; font-weight: bold; text-transform: uppercase; color: #2c3e50; letter-spacing: 1px; border-bottom: 1px solid #333; display: inline-block; padding-bottom: 3px; margin-bottom: 5px; }
        .doc-number { font-size: 14px; color: #555; }
        
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { vertical-align: top; border: none; padding: 0; }
        .info-box { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 12px; height: 100%; }
        .info-box-title { font-size: 12px; font-weight: bold; color: #7f8c8d; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .info-row { margin-bottom: 5px; display: table; width: 100%; }
        .info-label { display: table-cell; width: 100px; font-weight: bold; font-size: 12px; color: #34495e; }
        .info-value { display: table-cell; font-size: 13px; }
        
        .section-title { font-size: 14px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; text-transform: uppercase; border-left: 4px solid #3498db; padding-left: 8px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; }
        .data-table th { background-color: #2c3e50; color: #ffffff; font-weight: bold; text-align: left; font-size: 12px; text-transform: uppercase; }
        .data-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        
        .notes-box { background-color: #fff8e1; border-left: 4px solid #f39c12; padding: 10px; margin-bottom: 25px; font-size: 12px; }
        
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-table td { text-align: center; vertical-align: bottom; border: none; width: 33.33%; padding: 0 20px; }
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
        <div class="doc-title">Surat Perintah Kerja (SPK)</div>
        <div class="doc-number">No. SPK: {{ $workOrder->spk_number }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 48%; padding-right: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Informasi Pekerjaan</div>
                    <div class="info-row"><span class="info-label">No. Order</span><span class="info-value">: {{ $workOrder->order->no_order }}</span></div>
                    <div class="info-row"><span class="info-label">Tgl Persiapan</span><span class="info-value">: {{ \Carbon\Carbon::parse($workOrder->tanggal_persiapan)->format('d F Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Penanggung Jawab</span><span class="info-value">: {{ $workOrder->pic ? $workOrder->pic->name : '-' }}</span></div>
                </div>
            </td>
            <td style="width: 4%;">&nbsp;</td>
            <td style="width: 48%; padding-left: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Informasi Acara & Klien</div>
                    <div class="info-row"><span class="info-label">Nama Acara</span><span class="info-value">: {{ $workOrder->order->nama_acara }}</span></div>
                    <div class="info-row"><span class="info-label">Tgl Acara</span><span class="info-value">: {{ \Carbon\Carbon::parse($workOrder->order->tanggal)->format('d F Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Lokasi / Venue</span><span class="info-value">: {{ $workOrder->order->venue ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Klien</span><span class="info-value">: {{ $workOrder->order->customer ? $workOrder->order->customer->nama : '-' }}</span></div>
                </div>
            </td>
        </tr>
    </table>

    @if($workOrder->catatan)
    <div class="notes-box">
        <strong>Catatan Khusus SPK:</strong><br>
        {{ $workOrder->catatan }}
    </div>
    @endif

    <div class="section-title">Daftar Petugas / Crew Lapangan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%" class="text-center">No</th>
                <th width="45%">Nama Petugas</th>
                <th width="47%">Peran / Tugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrder->users as $index => $wu)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $wu->user ? $wu->user->name : '-' }}</strong></td>
                <td>{{ $wu->peran }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada petugas yang ditugaskan dalam SPK ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Disetujui Oleh,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Manajemen / Operasional</div>
            </td>
            <td>
                <!-- Kolom Kosong Tengah -->
            </td>
            <td>
                <div class="signature-title">Diterima Oleh,</div>
                <div class="signature-line"></div>
                <div class="signature-name">PIC / Koor. Lapangan</div>
            </td>
        </tr>
    </table>
</body>
</html>

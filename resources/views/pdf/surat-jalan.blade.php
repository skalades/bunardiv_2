<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $workOrder->surat_jalan_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 150px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 5px; text-decoration: underline; }
        .company-info { font-size: 12px; margin-bottom: 20px; }
        .row { width: 100%; display: table; }
        .col { display: table-cell; width: 50%; vertical-align: top; }
        .mb { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .signature-box { margin-top: 40px; width: 100%; display: table; }
        .signature-col { display: table-cell; width: 33.33%; text-align: center; }
        .signature-line { margin-top: 60px; border-bottom: 1px solid #333; width: 80%; margin-left: auto; margin-right: auto; }
    </style>
</head>
<body>
    <div class="header">
        @if($setting && $setting->company_logo)
            <img src="{{ public_path('storage/'.$setting->company_logo) }}" alt="Logo">
        @endif
        <div class="title">SURAT JALAN PENGIRIMAN</div>
        <div class="company-info">
            {{ $setting ? $setting->company_name : 'Catering System' }}<br>
            {{ $setting ? $setting->company_address : '' }}<br>
            {{ $setting ? $setting->company_phone : '' }}
        </div>
    </div>

    <div class="row mb">
        <div class="col">
            <strong>Nomor Surat Jalan:</strong> {{ $workOrder->surat_jalan_number }}<br>
            <strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($workOrder->tanggal_persiapan)->format('d-m-Y') }}<br>
            <strong>Pengirim (PIC):</strong> {{ $workOrder->pic ? $workOrder->pic->name : '-' }}
        </div>
        <div class="col text-right">
            <strong>Tujuan Pengiriman:</strong><br>
            {{ $workOrder->order->venue ?? '-' }}<br>
            <strong>Penerima / Klien:</strong> {{ $workOrder->order->customer ? $workOrder->order->customer->nama : '-' }}
        </div>
    </div>

    <div class="mb">
        <strong>Daftar Peralatan & Logistik:</strong>
        <p style="font-size: 12px; color: #555; margin-top: 5px;">Mohon periksa kembali kesesuaian jumlah barang sebelum dan sesudah acara.</p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="55%">Nama Barang / Peralatan</th>
                <th width="15%" class="text-center">Jumlah</th>
                <th width="25%" class="text-center">Ceklis Kirim</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrder->equipment as $index => $eq)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $eq->inventory ? $eq->inventory->nama : '-' }}</td>
                <td class="text-center">{{ $eq->jumlah }}</td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data peralatan untuk dikirim.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-box">
        <div class="signature-col">
            Dikeluarkan Oleh,<br>
            Gudang / Logistik<br>
            <div class="signature-line"></div>
        </div>
        <div class="signature-col">
            Pengirim / Driver,<br>
            <br>
            <div class="signature-line"></div>
        </div>
        <div class="signature-col">
            Penerima,<br>
            Klien / Perwakilan<br>
            <div class="signature-line"></div>
        </div>
    </div>
</body>
</html>

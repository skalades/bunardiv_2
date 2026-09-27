<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Kerja (SPK) - {{ $workOrder->spk_number }}</title>
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
        <div class="title">SURAT PERINTAH KERJA (SPK)</div>
        <div class="company-info">
            {{ $setting ? $setting->company_name : 'Catering System' }}<br>
            {{ $setting ? $setting->company_address : '' }}<br>
            {{ $setting ? $setting->company_phone : '' }}
        </div>
    </div>

    <div class="row mb">
        <div class="col">
            <strong>Nomor SPK:</strong> {{ $workOrder->spk_number }}<br>
            <strong>Tanggal Persiapan:</strong> {{ \Carbon\Carbon::parse($workOrder->tanggal_persiapan)->format('d-m-Y') }}<br>
            <strong>PIC Penanggung Jawab:</strong> {{ $workOrder->pic ? $workOrder->pic->name : '-' }}
        </div>
        <div class="col text-right">
            <strong>Nomor Order:</strong> {{ $workOrder->order->no_order }}<br>
            <strong>Nama Acara:</strong> {{ $workOrder->order->nama_acara }}<br>
            <strong>Tanggal Acara:</strong> {{ \Carbon\Carbon::parse($workOrder->order->tanggal)->format('d-m-Y') }}<br>
            <strong>Venue / Lokasi:</strong> {{ $workOrder->order->venue ?? '-' }}
        </div>
    </div>

    <div class="mb">
        <strong>Detail Klien / Pemesan:</strong><br>
        Nama: {{ $workOrder->order->customer ? $workOrder->order->customer->nama : '-' }}<br>
        Telepon: {{ $workOrder->order->customer ? $workOrder->order->customer->no_telp : '-' }}
    </div>

    @if($workOrder->catatan)
    <div class="mb">
        <strong>Catatan Khusus:</strong><br>
        {{ $workOrder->catatan }}
    </div>
    @endif

    <div class="mb">
        <strong>Daftar Petugas / Crew:</strong>
    </div>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="45%">Nama Petugas</th>
                <th width="50%">Peran / Tugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrder->users as $index => $wu)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $wu->user ? $wu->user->name : '-' }}</td>
                <td>{{ $wu->peran }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada petugas yang ditugaskan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-box">
        <div class="signature-col">
            Disetujui Oleh,<br>
            Manajemen<br>
            <div class="signature-line"></div>
        </div>
        <div class="signature-col">
        </div>
        <div class="signature-col">
            Diterima Oleh,<br>
            PIC / Koor. Lapangan<br>
            <div class="signature-line"></div>
        </div>
    </div>
</body>
</html>

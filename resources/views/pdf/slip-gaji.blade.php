<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $user->name }} - {{ $payroll->nama_bulan }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #2c3e50;
            margin: 0;
            padding: 0;
        }
        .container {
            border: 2px solid #cfa24b;
            padding: 24px;
            position: relative;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #cfa24b;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #cfa24b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-contact {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }
        .doc-title {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-subtitle {
            text-align: right;
            font-size: 11px;
            color: #7f8c8d;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            font-size: 11px;
            border: none;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 15%;
        }
        .info-sep {
            width: 2%;
        }
        .info-val {
            color: #2c3e50;
            width: 33%;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .details-table th {
            background: #f8f5eb;
            color: #8c6d23;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            padding: 6px 10px;
            border-top: 1px solid #e2d3b3;
            border-bottom: 1px solid #e2d3b3;
            text-transform: uppercase;
        }
        .details-table td {
            padding: 6px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f0f0f0;
        }
        .amount-col {
            text-align: right;
            font-weight: bold;
        }
        .summary-box {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 8px 12px;
            border: none;
        }
        .thp-box {
            background: #fdfaf2;
            border: 2px solid #cfa24b;
            padding: 10px 16px;
            text-align: right;
            border-radius: 4px;
        }
        .thp-label {
            font-size: 11px;
            color: #8c6d23;
            text-transform: uppercase;
            font-weight: bold;
        }
        .thp-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 2px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            border: none;
        }
        .signature-title {
            font-size: 11px;
            font-weight: bold;
            color: #555;
            margin-bottom: 50px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 160px;
            margin: 0 auto 4px auto;
        }
        .signature-name {
            font-size: 11px;
            color: #333;
            font-weight: bold;
        }
        .badge-paid {
            display: inline-block;
            background: #e8f8f0;
            color: #27ae60;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <div class="company-name">{{ $setting ? $setting->company_name : 'N7 DECORATION & CATERING' }}</div>
                    <div class="company-contact">
                        {{ $setting ? $setting->company_address : 'Yogyakarta' }}<br>
                        Telp/WA: {{ $setting ? $setting->company_phone : '-' }}
                    </div>
                </td>
                <td style="width: 45%;">
                    <div class="doc-title">SLIP GAJI</div>
                    <div class="doc-subtitle">Periode: {{ $payroll->nama_bulan }}</div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="info-label">Nama Karyawan</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $user->name }}</strong></td>

                <td class="info-label">NIK</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $user->employeeProfile->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Jabatan / Posisi</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $user->employeeProfile->jabatan ?? 'Staff Lapangan' }}</td>

                <td class="info-label">Status Kepegawaian</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    {{ ucfirst($item->tipe_karyawan) }} 
                    ({{ $item->tipe_karyawan === 'bulanan' ? $item->jumlah_hari_hadir . ' Hari Hadir' : $item->jumlah_event_hadir . ' Event/SPK' }})
                </td>
            </tr>
            <tr>
                <td class="info-label">Status Pembayaran</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    @if($item->status === 'paid')
                        <span class="badge-paid">LUNAS ({{ $item->metode_bayar ?? 'Transfer' }})</span>
                    @else
                        <span style="color: #e67e22; font-weight: bold;">DRAFT / BELUM DIBAYAR</span>
                    @endif
                </td>

                <td class="info-label">Tanggal Bayar</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $item->tanggal_dibayar ? \Carbon\Carbon::parse($item->tanggal_dibayar)->format('d M Y') : '-' }}</td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
            <tr>
                <!-- Kolom Penerimaan -->
                <td style="width: 50%; vertical-align: top; padding-right: 10px; border: none;">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Penerimaan (Pendapatan)</th>
                                <th class="amount-col">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($item->tipe_karyawan === 'bulanan')
                                <tr>
                                    <td>Gaji Pokok Bulanan</td>
                                    <td class="amount-col">Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td>Upah Kehadiran Event ({{ $item->jumlah_event_hadir }} SPK)</td>
                                    <td class="amount-col">Rp {{ number_format($item->upah_event, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($item->tunjangan_transport > 0)
                                <tr>
                                    <td>Tunjangan Transport</td>
                                    <td class="amount-col">Rp {{ number_format($item->tunjangan_transport, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($item->tunjangan_makan > 0)
                                <tr>
                                    <td>Tunjangan Makan</td>
                                    <td class="amount-col">Rp {{ number_format($item->tunjangan_makan, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($item->bonus > 0)
                                <tr>
                                    <td>Bonus</td>
                                    <td class="amount-col">Rp {{ number_format($item->bonus, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($item->lembur > 0)
                                <tr>
                                    <td>Uang Lembur</td>
                                    <td class="amount-col">Rp {{ number_format($item->lembur, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr style="background: #fafafa; font-weight: bold;">
                                <td><strong>Total Penerimaan Bruto</strong></td>
                                <td class="amount-col"><strong>Rp {{ number_format($item->total_bruto, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- Kolom Potongan -->
                <td style="width: 50%; vertical-align: top; padding-left: 10px; border: none;">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Potongan</th>
                                <th class="amount-col">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Cicilan Kasbon / Pinjaman</td>
                                <td class="amount-col" style="color: #c0392b;">
                                    @if($item->potongan_kasbon > 0)
                                        (Rp {{ number_format($item->potongan_kasbon, 0, ',', '.') }})
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                            </tr>
                            @if($item->potongan_lain > 0)
                                <tr>
                                    <td>Potongan Lain / Penyesuaian</td>
                                    <td class="amount-col" style="color: #c0392b;">(Rp {{ number_format($item->potongan_lain, 0, ',', '.') }})</td>
                                </tr>
                            @endif
                            <tr style="background: #fafafa; font-weight: bold;">
                                <td><strong>Total Potongan</strong></td>
                                <td class="amount-col" style="color: #c0392b;">
                                    <strong>(Rp {{ number_format($item->potongan_kasbon + $item->potongan_lain, 0, ',', '.') }})</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Box Take Home Pay -->
        <table class="summary-box">
            <tr>
                <td style="width: 50%;">
                    @if($item->catatan)
                        <div style="font-size: 10px; color: #7f8c8d; font-style: italic;">
                            Catatan: {{ $item->catatan }}
                        </div>
                    @endif
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="thp-box">
                        <div class="thp-label">Gaji Bersih Diterima (Take Home Pay)</div>
                        <div class="thp-value">Rp {{ number_format($item->total_bersih, 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-title">Diterima oleh Karyawan,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $user->name }}</div>
                </td>
                <td>
                    <div class="signature-title">Disetujui oleh HRD / Keuangan,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $setting ? $setting->company_name : 'N7 Decoration' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

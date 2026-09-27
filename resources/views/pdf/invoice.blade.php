<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->no_order }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 150px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .company-info { font-size: 12px; margin-bottom: 20px; }
        .row { width: 100%; display: table; }
        .col { display: table-cell; width: 50%; }
        .mb { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if($setting && $setting->company_logo)
            <img src="{{ public_path('storage/'.$setting->company_logo) }}" alt="Logo">
        @endif
        <div class="title">INVOICE</div>
        <div class="company-info">
            {{ $setting ? $setting->company_name : 'Nama Perusahaan' }}<br>
            {{ $setting ? $setting->company_address : '' }}<br>
            Telp: {{ $setting ? $setting->company_phone : '' }} | Email: {{ $setting ? $setting->company_email : '' }}
        </div>
    </div>

    <div class="row mb">
        <div class="col">
            <strong>Kepada:</strong><br>
            {{ $order->customer->nama ?? '-' }}<br>
            {{ $order->customer->alamat ?? '-' }}<br>
            Telp: {{ $order->customer->no_wa ?? '-' }}
        </div>
        <div class="col text-right">
            <strong>No. Order:</strong> {{ $order->no_order }}<br>
            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->tanggal)->format('d M Y') }}<br>
            <strong>Acara:</strong> {{ $order->nama_acara }}<br>
            <strong>Status:</strong> {{ strtoupper($order->status) }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->nama ?? '-' }}</td>
                <td>{{ $item->qty }}</td>
                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right bold">Subtotal</td>
                <td class="text-right bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($order->diskon > 0)
            <tr>
                <td colspan="3" class="text-right bold">Diskon</td>
                <td class="text-right bold">-Rp {{ number_format($order->diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->biaya_tambahan > 0)
            <tr>
                <td colspan="3" class="text-right bold">Biaya Tambahan</td>
                <td class="text-right bold">Rp {{ number_format($order->biaya_tambahan, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" class="text-right bold">TOTAL</td>
                <td class="text-right bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb">
        <strong>Historis Pembayaran:</strong>
        @if($order->payments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th>Referensi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->payments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->reference_number }}</td>
                        <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right bold">Sisa Tagihan</td>
                        <td class="text-right bold">Rp {{ number_format($order->total - $order->payments->sum('amount'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p>Belum ada pembayaran.</p>
        @endif
    </div>

    @if($setting && $setting->bank_accounts)
    <div class="mb">
        <strong>Informasi Pembayaran (Transfer):</strong><br>
        @foreach($setting->bank_accounts as $bank)
            {{ $bank['bank_name'] }} - {{ $bank['account_number'] }} a.n {{ $bank['account_name'] }}<br>
        @endforeach
    </div>
    @endif

    @if($setting && $setting->invoice_notes)
    <div style="font-size: 11px; color: #555; margin-top: 30px;">
        <strong>Catatan / Syarat & Ketentuan:</strong><br>
        {!! nl2br(e($setting->invoice_notes)) !!}
    </div>
    @endif
</body>
</html>

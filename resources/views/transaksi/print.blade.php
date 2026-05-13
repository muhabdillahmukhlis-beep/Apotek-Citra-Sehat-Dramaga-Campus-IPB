<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaksi->no_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm; /* Standar printer thermal */
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; }
        .info { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { text-align: left; border-bottom: 1px dashed #000; padding: 5px 0; }
        td { padding: 5px 0; vertical-align: top; }
        .totals { border-top: 1px dashed #000; padding-top: 5px; }
        .totals div { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .footer { margin-top: 20px; font-size: 10px; }
        
        @print {
            @page { margin: 0; }
            body { margin: 0.5cm; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header text-center">
        <h2>APOTEK SEHAT</h2>
        <p>Jl. Kesehatan No. 123, Kota Anda<br>Telp: 0812-3456-7890</p>
    </div>

    <div class="info">
        <div>No: {{ $transaksi->no_transaksi }}</div>
        <div>Tgl: {{ $transaksi->created_at->format('d/m/Y H:i') }}</div>
        <div>Kasir: {{ $transaksi->kasir->name ?? 'Admin' }}</div>
        <div>Metode: {{ $transaksi->metode_bayar }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->details as $item)
            <tr>
                <td>{{ $item->nama_obat }}</td>
                <td class="text-center">{{ $item->jumlah }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
        </div>
        <div style="font-weight: bold; font-size: 14px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
        </div>
        
        @if($transaksi->metode_bayar == 'Tunai')
        <div style="margin-top: 5px;">
            <span>Bayar:</span>
            <span>Rp {{ number_format($transaksi->uang_diterima, 0, ',', '.') }}</span>
        </div>
        <div>
            <span>Kembali:</span>
            <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="footer text-center">
        <p>Terima Kasih Atas Kunjungan Anda<br>Semoga Lekas Sembuh</p>
        <p style="margin-top: 10px;">{{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
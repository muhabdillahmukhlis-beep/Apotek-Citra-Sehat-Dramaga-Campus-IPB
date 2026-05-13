<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Apotek Citra Sehat</title>
    <style>
        /* CSS sederhana untuk PDF */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .title {
            margin: 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2 class="title">Apotek Citra Sehat</h2>
        <p>Jl. Raya Alamat Apotek No. 123 | Telp: (021) 1234567</p>
        <h3>LAPORAN RIWAYAT TRANSAKSI</h3>
    </div>

    <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->no_transaksi }}</td>
                <td>{{ $row->created_at->format('d/m/Y') }}</td>
                <td>{{ $row->kasir->name ?? 'Sistem' }}</td>
                <td>{{ ucfirst($row->metode_bayar) }}</td>
                <td class="text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
            </tr>
            @php $grandTotal += $row->total; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="5" class="text-right">GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Bogor, {{ date('d F Y') }}</p>
        <br><br><br>
        <p>( ____________________ )</p>
        <p>Administrator</p>
    </div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($tab) }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { margin: 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 11px; }
        th { background-color: #f8f8f8; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 10px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan {{ $tab }} Apotek</h2>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>No. Transaksi</th>
                <th>Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSemua = 0; @endphp
            @foreach($riwayat as $index => $t)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>#{{ $t->no_transaksi }}</td>
                <td class="text-right">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            @php $totalSemua += $t->total; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($totalSemua, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>

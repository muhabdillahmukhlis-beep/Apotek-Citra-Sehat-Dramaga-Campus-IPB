<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Logika filter yang sama dengan Controller
        $start = \Carbon\Carbon::parse($this->request['start'])->startOfDay();
        $end = \Carbon\Carbon::parse($this->request['end'])->endOfDay();

        return Transaksi::with('kasir')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'selesai')
            ->get();
    }

    public function headings(): array
    {
        return ['Waktu', 'No. Transaksi', 'Kasir', 'Total Tagihan'];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->created_at->format('d/m/Y H:i'),
            '#' . $transaksi->no_transaksi,
            $transaksi->kasir->name ?? 'Admin',
            $transaksi->total,
        ];
    }
}
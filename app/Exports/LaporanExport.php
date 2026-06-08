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
        // CARA AMAN: Membaca parameter dari Object Request dengan nilai default sebulan penuh jika kosong
        $startDate = $this->request->input('start') ?? now()->startOfMonth()->toDateString();
        $endDate = $this->request->input('end') ?? now()->endOfMonth()->toDateString();

        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

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
            // Mengubah nama kolom penampung nama kasir dari 'name' menjadi 'nama' menyesuaikan database lokal Anda
            $transaksi->kasir->nama ?? 'Admin',
            $transaksi->total,
        ];
    }
}
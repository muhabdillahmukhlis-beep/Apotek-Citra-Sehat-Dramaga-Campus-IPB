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
        // Solusi Mutakhir: Mendukung format Array maupun Object, dengan fallback sebulan penuh jika kosong
        $startDate = is_array($this->request) 
            ? ($this->request['start'] ?? null) 
            : ($this->request->input('start') ?? null);

        $endDate = is_array($this->request) 
            ? ($this->request['end'] ?? null) 
            : ($this->request->input('end') ?? null);

        // Jika form kosong, set otomatis ke awal dan akhir bulan ini
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

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
            $transaksi->kasir->nama ?? 'Admin',
            $transaksi->total,
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function query()
    {
        $query = Transaksi::query()->with('kasir');

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $this->request->start_date . " 00:00:00", 
                $this->request->end_date . " 23:59:59"
            ]);
        }

        return $query;
    }

    public function headings(): array {
        return ["No Transaksi", "Tanggal", "Kasir", "Metode Bayar", "Total"];
    }

    public function map($transaksi): array {
        return [
            $transaksi->no_transaksi,
            $transaksi->created_at->format('d/m/Y'),
            $transaksi->kasir->name ?? '-',
            $transaksi->metode_bayar,
            $transaksi->total,
        ];
    }
}
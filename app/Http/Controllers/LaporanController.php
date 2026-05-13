<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Obat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
// Tambahkan Import di bawah ini
use Barryvdh\DomPDF\Facade\Pdf; 
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // Fungsi pembantu untuk mengambil data berdasarkan filter yang sama
    private function getFilteredData(Request $request)
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        if ($request->range == 'hari_ini') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($request->range == 'bulan_ini') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($request->filled('start') && $request->filled('end')) {
            $startDate = Carbon::parse($request->start)->startOfDay();
            $endDate = Carbon::parse($request->end)->endOfDay();
        }

        return [$startDate, $endDate];
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'penjualan');
        $range = $request->get('range', 'minggu_ini');
        [$startDate, $endDate] = $this->getFilteredData($request);

        $data = [
            'tab'       => $tab,
            'range'     => $range,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate'   => $endDate->format('Y-m-d'),
            'totalPendapatan' => 0,
            'totalTransaksi'  => 0,
            'rataRata'        => 0,
            'obatTerlaris'    => null,
            'riwayat'         => collect()
        ];

        if ($tab == 'penjualan' || $tab == 'profit') {
            $query = Transaksi::whereBetween('created_at', [$startDate, $endDate])->where('status', 'selesai');
            
            $data['totalPendapatan'] = $query->sum('total');
            $data['totalTransaksi'] = $query->count();
            $data['rataRata'] = $data['totalTransaksi'] > 0 ? $data['totalPendapatan'] / $data['totalTransaksi'] : 0;
            
            $data['obatTerlaris'] = DetailTransaksi::select('nama_obat', DB::raw('SUM(jumlah) as total_terjual'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('nama_obat')
                ->orderBy('total_terjual', 'desc')
                ->first();

            $data['riwayat'] = Transaksi::with(['kasir'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->paginate(10)
                ->withQueryString();

            if ($tab == 'profit') {
                $data['totalProfit'] = $data['totalPendapatan'] * 0.2; 
            }
        } elseif ($tab == 'stok') {
            $data['totalObat'] = Obat::count();
            $data['stokRendah'] = Obat::where('stok', '<', 10)->get();
            // Gunakan try-catch atau check jika table kategori_obat benar ada
            $data['totalKategori'] = DB::table('kategori_obat')->count();
            $data['hampirKadaluarsa'] = Obat::whereBetween('tgl_kadaluarsa', [now(), now()->addMonths(3)])->count();
        }

        return view('laporan.index', $data);
    }

    public function exportPdf(Request $request) 
    { 
        [$startDate, $endDate] = $this->getFilteredData($request);
        $tab = $request->get('tab', 'penjualan');

        $riwayat = Transaksi::with('kasir')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->get();

        $pdf = Pdf::loadView('laporan.pdf_template', [
            'riwayat' => $riwayat,
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'tab' => $tab
        ]);

        return $pdf->download("Laporan_{$tab}_" . now()->format('Ymd') . ".pdf");
    }

    public function exportExcel(Request $request) 
{ 
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\LaporanExport($request->all()), 
        'laporan-penjualan.xlsx'
    );
}
}
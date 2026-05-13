<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Import Library Export
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport; 

class TransaksiController extends Controller
{
    /**
     * HALAMAN RIWAYAT (DENGAN FILTER)
     */
    public function index(Request $request)
    {
        // Menggunakan helper filter agar konsisten
        $query = $this->applyFilter($request);

        $kasirList = User::all(); 
        $riwayat = $query->latest()->paginate(10)->withQueryString();

        return view('transaksi.index', compact('riwayat', 'kasirList'));
    }

    /**
     * FUNGSI HELPER UNTUK FILTER
     * Digunakan oleh index, exportPdf, dan exportExcel
     */
    private function applyFilter(Request $request)
    {
        $query = Transaksi::with('kasir');

        // Filter Rentang Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . " 00:00:00", 
                $request->end_date . " 23:59:59"
            ]);
        }

        // Filter Lainnya
        if ($request->filled('kasir_id')) $query->where('kasir_id', $request->kasir_id);
        if ($request->filled('metode')) $query->where('metode_bayar', $request->metode);
        if ($request->filled('status')) $query->where('status', $request->status);

        return $query;
    }

    /**
     * EXPORT PDF
     */
    public function exportPdf(Request $request)
    {
        $query = $this->applyFilter($request);
        $data = $query->latest()->get();

        // Menggunakan view transaksi.export_pdf (sesuaikan dengan nama file blade Anda)
        $pdf = Pdf::loadView('transaksi.export_pdf', compact('data'));
        
        return $pdf->download('laporan-transaksi-'.now()->format('d-m-Y').'.pdf');
    }

    /**
     * EXPORT EXCEL
     */
    public function exportExcel(Request $request) 
    {
        return Excel::download(new TransaksiExport($request), 'laporan-transaksi-'.now()->format('d-m-Y').'.xlsx');
    }

    /**
     * API UNTUK MODAL DETAIL (AJAX)
     */
    public function showDetail($id)
    {
        try {
            $transaksi = Transaksi::with(['kasir', 'details'])->findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data'   => $transaksi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data transaksi tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * HALAMAN CETAK STRUK
     */
    public function print($id)
    {
        $transaksi = Transaksi::with(['kasir', 'details'])->findOrFail($id);
        return view('transaksi.print', compact('transaksi'));
    }

    public function create()
    {
        $obatList = Obat::where('is_aktif', true)
                        ->where('stok', '>', 0)
                        ->orderBy('nama', 'asc')
                        ->get();
        return view('transaksi.create', compact('obatList'));
    }

    /**
     * PROSES SIMPAN TRANSAKSI
     */
    public function store(Request $request) 
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:obat,id', 
            'items.*.qty' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|string', 
            'bayar' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $total = 0;
                $itemsToProcess = [];

                foreach ($request->items as $item) {
                    $obat = Obat::lockForUpdate()->findOrFail($item['id']);

                    if ($obat->stok < $item['qty']) {
                        throw new \Exception("Stok obat '{$obat->nama}' tidak mencukupi!");
                    }

                    $subtotalItem = $obat->harga_jual * $item['qty'];
                    $total += $subtotalItem;

                    $itemsToProcess[] = [
                        'obat' => $obat,
                        'qty' => $item['qty'],
                        'subtotal' => $subtotalItem
                    ];
                }

                if ($request->metode_pembayaran === 'Tunai' && $request->bayar < $total) {
                    throw new \Exception("Uang pembayaran kurang! Total: Rp " . number_format($total, 0, ',', '.'));
                }

                $transaksi = Transaksi::create([
                    'no_transaksi'  => 'TRX-' . now()->format('YmdHis') . strtoupper(Str::random(4)),
                    'kasir_id'      => Auth::id() ?? 1,
                    'metode_bayar'  => $request->metode_pembayaran, 
                    'uang_diterima' => $request->bayar,
                    'status'        => 'selesai',
                    'subtotal'      => $total,
                    'total'         => $total,
                    'kembalian'     => $request->metode_pembayaran === 'Tunai' ? ($request->bayar - $total) : 0
                ]);

                foreach ($itemsToProcess as $process) {
                    $obat = $process['obat'];
                    
                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'obat_id'      => $obat->id,
                        'nama_obat'    => $obat->nama, 
                        'kode_obat'    => $obat->kode_obat,
                        'satuan'       => $obat->satuan,
                        'harga_satuan' => $obat->harga_jual,
                        'jumlah'       => $process['qty'],
                        'subtotal'     => $process['subtotal']
                    ]);

                    $obat->decrement('stok', $process['qty']);
                }

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Transaksi Berhasil Disimpan!',
                    'redirect' => route('transaksi.index')
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * CLEAR RIWAYAT
     */
    public function clear()
    {
        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DetailTransaksi::truncate(); 
            Transaksi::truncate(); 
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

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
     * HALAMAN RIWAYAT (DENGAN FILTER KASIR FIX)
     */
    public function index(Request $request)
    {
        // Menggunakan helper filter agar konsisten
        $query = $this->applyFilter($request);

        /**
         * 🌟 PERBAIKAN:
         * Menghapus klausa pencarian kolom 'status' yang memicu query error 1054.
         * Mengambil data user berdasarkan hak akses (role) dan diurutkan alfabetis (A-Z).
         */
        $kasirList = User::whereIn('role', ['admin', 'kasir']) 
                         ->orderBy('nama', 'asc')
                         ->get(); 
                         
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

        // Filter Berdasarkan Kasir Pembuat
        if ($request->filled('kasir_id')) {
            $query->where('kasir_id', $request->kasir_id);
        }
        
        // Filter Berdasarkan Metode Pembayaran
        if ($request->filled('metode')) {
            $query->where('metode_bayar', $request->metode);
        }
        
        // Filter Berdasarkan Status Transaksi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    /**
     * EXPORT PDF
     */
    public function exportPdf(Request $request)
    {
        $query = $this->applyFilter($request);
        $data = $query->latest()->get();

        // Menggunakan view transaksi.export_pdf
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
                'data'    => $transaksi
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

    /**
     * HALAMAN FORM TRANSAKSI BARU
     */
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
     * Dilengkapi Interupsi Otomatis Batas Stok & Uang Belanja (TC_TRX_003 & TC_TRX_006)
     */
    public function store(Request $request) 
    {
        // 1. Validasi Awal Struktur Request Input Kasir
        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.id'          => 'required|exists:obat,id', 
            'items.*.qty'         => 'required|integer|min:1',
            'metode_pembayaran'   => 'required|string|in:Tunai,QRIS,Debit', 
            'bayar'               => 'required|numeric|min:0',
        ], [
            'items.required'         => 'Keranjang belanja kosong! Pilih minimal 1 obat.',
            'items.*.qty.min'        => 'Kuantitas beli minimal bernilai 1.',
            'metode_pembayaran.in'   => 'Metode pembayaran harus berupa Tunai, QRIS, atau Debit.',
            'bayar.min'              => 'Uang pembayaran tidak boleh bernilai negatif.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $total = 0;
                $itemsToProcess = [];

                // INTERUPSI 1: Pengecekan sisa stok fisik obat di database (TC_TRX_003)
                foreach ($request->items as $item) {
                    // Menggunakan lockForUpdate untuk menghindari Race Condition (Tabrakan Stok)
                    $obat = Obat::lockForUpdate()->findOrFail($item['id']);

                    if ($obat->stok < $item['qty']) {
                        // Sistem memblokir dan melempar pesan kesalahan spesifik
                        throw new \Exception("Aksi Diblokir! Stok obat '{$obat->nama}' tidak mencukupi. Sisa stok di sistem: {$obat->stok} unit, permintaan: {$item['qty']} unit.");
                    }

                    $subtotalItem = $obat->harga_jual * $item['qty'];
                    $total += $subtotalItem;

                    $itemsToProcess[] = [
                        'obat' => $obat,
                        'qty' => $item['qty'],
                        'subtotal' => $subtotalItem
                    ];
                }

                // INTERUPSI 2: Pengecekan kecukupan uang tunai (TC_TRX_006)
                if ($request->metode_pembayaran === 'Tunai' && $request->bayar < $total) {
                    $kekurangan = $total - $request->bayar;
                    throw new \Exception("Aksi Diblokir! Uang tunai kurang sebesar Rp " . number_format($kekurangan, 0, ',', '.') . " (Total Belanja: Rp " . number_format($total, 0, ',', '.') . ")");
                }

                // 2. Eksekusi Pembuatan Dokumen Transaksi Utama jika lolos filter interupsi
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

                // 3. Rekam Detail Item dan Potong Stok Gudang secara Sinkron
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

                    // Memotong stok obat aman dari minus
                    $obat->decrement('stok', $process['qty']);
                }

                $formattedKembali = "Rp " . number_format($transaksi->kembalian, 0, ',', '.');

                return response()->json([
                    'status'   => 'success', 
                    'message'  => "Transaksi Berhasil Disimpan! Kembalian: {$formattedKembali}",
                    'redirect' => route('transaksi.index')
                ]);
            }); 
        } catch (\Exception $e) {
            // Mengembalikan pesan interupsi dalam bentuk JSON Error (HTTP 422)
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * CLEAR RIWAYAT (RESET TRANSAKSI)
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
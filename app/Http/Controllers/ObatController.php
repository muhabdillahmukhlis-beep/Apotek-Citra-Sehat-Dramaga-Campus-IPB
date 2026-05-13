<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\KategoriObat;
use App\Models\Supplier;
use Illuminate\Http\Request;

// Tambahan untuk fitur Excel
use App\Exports\ObatExport;
use App\Imports\ObatImport;
use Maatwebsite\Excel\Facades\Excel;

class ObatController extends Controller
{
    // 1. Menampilkan Daftar Obat (Index)
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori');
        $status = $request->input('status');

        $query = Obat::with('kategori');

        // Filter: Pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_obat', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter: Kategori
        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        // Filter: Status Stok
        if ($status) {
            if ($status === 'habis') {
                $query->where('stok', '<=', 0);
            } elseif ($status === 'menipis') {
                $query->whereBetween('stok', [1, 10]);
            } elseif ($status === 'aman') {
                $query->where('stok', '>', 10);
            }
        }

        $obat = $query->orderBy('created_at', 'desc')->get(); 
        $kategoriData = KategoriObat::all(); 
        
        // PERBAIKAN: Mengirim $kategori DAN $categories agar Index & Edit tidak error
        return view('obat.index', [
            'obat' => $obat,
            'kategori' => $kategoriData,    // Dibutuhkan jika ada modal/form edit di index
            'categories' => $kategoriData   // Dibutuhkan oleh @foreach($categories) di file index.blade.php
        ]);
    }
    
    // 2. Menampilkan Form Tambah
    public function create()
    {
        $kategori = KategoriObat::all();
        $supplier = Supplier::all();

        // Mengirim dengan kedua nama untuk keamanan
        return view('obat.create', [
            'kategori' => $kategori,
            'categories' => $kategori,
            'supplier' => $supplier
        ]);
    }

    // 3. Menyimpan Data Obat Baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_obat'      => 'required|unique:obat',
            'nama'           => 'required',
            'id_kategori'    => 'required|exists:kategori_obat,id',
            'stok'           => 'required|numeric',
            'harga_beli'     => 'required|numeric',
            'harga_jual'     => 'required|numeric',
            'tgl_kadaluarsa' => 'required|date',
            'satuan'         => 'required',
        ]);

        $data = $request->all();
        // Memasukkan id_kategori dari form ke kolom kategori_id di database
        $data['kategori_id'] = $request->id_kategori;

        Obat::create($data);

        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit
    public function edit(string $id) 
    {
        $obat = Obat::findOrFail($id);
        $kategori = KategoriObat::all();
        $supplier = Supplier::all();
        
        // PERBAIKAN: Mengirim variabel $kategori agar cocok dengan @foreach($kategori) di edit.blade.php
        return view('obat.edit', [
            'obat' => $obat,
            'kategori' => $kategori,
            'categories' => $kategori, // Jaga-jaga jika di edit.blade juga ada yang pakai nama categories
            'supplier' => $supplier
        ]);
    }

    // 5. Memproses Update Data
    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id);
        
        $request->validate([
            'kode_obat'      => 'required|unique:obat,kode_obat,'.$id,
            'nama'           => 'required',
            'id_kategori'    => 'required|exists:kategori_obat,id', 
            'satuan'         => 'required',
            'stok'           => 'required|numeric',
            'harga_beli'     => 'required|numeric',
            'harga_jual'     => 'required|numeric',
            'tgl_kadaluarsa' => 'required|date',
        ]);

        $data = $request->all();
        $data['kategori_id'] = $request->id_kategori; 
        
        $obat->update($data);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui!');
    }

    // 6. Menghapus Obat
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus!');
    }

    // --- FITUR EXCEL ---

    public function exportExcel() 
    {
        try {
            return Excel::download(new ObatExport, 'data-obat-'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    public function importExcel(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new ObatImport, $request->file('file'));
            return redirect()->route('obat.index')->with('success', 'Data obat berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('obat.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\KategoriObat;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        // Filter: Pencarian (TC_OBAT_007)
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
        
        return view('obat.index', [
            'obat' => $obat,
            'kategori' => $kategoriData,    
            'categories' => $kategoriData   
        ]);
    }
    
    // 2. Menampilkan Form Tambah
    public function create()
    {
        $kategori = KategoriObat::all();
        $supplier = Supplier::all();

        return view('obat.create', [
            'kategori' => $kategori,
            'categories' => $kategori,
            'supplier' => $supplier
        ]);
    }

    // 3. Menyimpan Data Obat Baru
    public function store(Request $request)
    {
        // Pastikan konversi matematika murni diletakkan di baris paling atas rute
        $stokInput = (int)$request->input('stok', 0);
        $hargaBeliInput = (float)$request->input('harga_beli', 0);
        $hargaJualInput = (float)$request->input('harga_jual', 0);

        // Pengaman manual matematika murni sebelum validator internal
        if ($stokInput <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal Menambahkan! Stok awal obat baru wajib di atas angka 0.');
        }

        if ($hargaBeliInput < 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal Menambahkan! Harga beli tidak boleh bernilai negatif.');
        }

        if ($hargaJualInput < 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal Menambahkan! Harga jual tidak boleh bernilai negatif.');
        }

        $request->validate([
            'kode_obat'      => 'required|unique:obat,kode_obat',
            'nama'           => 'required|string|max:255|unique:obat,nama', 
            'id_kategori'    => 'required|exists:kategori_obat,id',          
            'satuan'         => 'required|string',
            'harga_beli'     => 'required|numeric|min:0',                   
            'harga_jual'     => 'required|numeric|min:0',                   
            'tgl_kadaluarsa' => 'required|date',
        ], [
            'kode_obat.required'      => 'Kode obat wajib diisi.',
            'kode_obat.unique'        => 'Kode obat sudah digunakan oleh item lain.',
            'nama.required'           => 'Nama obat tidak boleh kosong.',
            'nama.unique'             => 'Gagal! Nama obat sudah terdaftar di apotek.',
            'id_kategori.required'    => 'Kategori obat wajib dipilih.',
            'id_kategori.exists'      => 'Kategori yang dipilih tidak valid.',
            'satuan.required'         => 'Satuan obat wajib diisi.',
            'harga_beli.required'     => 'Harga beli wajib diisi.',
            'harga_beli.min'          => 'Gagal! Harga beli tidak boleh bernilai negatif.',
            'harga_jual.required'     => 'Harga jual wajib diisi.',
            'harga_jual.min'          => 'Gagal! Harga jual tidak boleh bernilai negatif.',
            'tgl_kadaluarsa.required' => 'Tanggal kadaluarsa wajib diisi.',
            'tgl_kadaluarsa.date'     => 'Format tanggal kadaluarsa tidak valid.',
        ]);

        // Menyusun data secara aman satu per satu untuk mencegah bypass Mass Assignment
        $obat = new Obat();
        $obat->kode_obat      = $request->kode_obat;
        $obat->nama           = $request->nama;
        $obat->kategori_id    = $request->id_kategori;
        $obat->satuan         = $request->satuan;
        $obat->stok           = $stokInput; 
        $obat->harga_beli     = $hargaBeliInput;
        $obat->harga_jual     = $hargaJualInput;
        $obat->tgl_kadaluarsa = $request->tgl_kadaluarsa;
        
        $obat->save();

        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit
    public function edit(string $id) 
    {
        $obat = Obat::findOrFail($id);
        $kategori = KategoriObat::all();
        $supplier = Supplier::all();
        
        return view('obat.edit', [
            'obat' => $obat,
            'kategori' => $kategori,
            'categories' => $kategori, 
            'supplier' => $supplier
        ]);
    }

    // 5. Memproses Update Data
    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id);
        
        $request->validate([
            'kode_obat'      => 'required|unique:obat,kode_obat,'.$id,
            'nama'           => ['required', 'string', 'max:255', Rule::unique('obat', 'nama')->ignore($id)],
            'id_kategori'    => 'required|exists:kategori_obat,id', 
            'satuan'         => 'required|string',
            
            // Logika kustom matematika murni untuk form edit data
            'stok'           => [
                'required',
                function ($attribute, $value, $fail) {
                    if ((int)$value < 0) {
                        $fail('Stok tidak boleh bernilai negatif.');
                    }
                }
            ],
            'harga_beli'     => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ((float)$value < 0) {
                        $fail('Harga beli tidak boleh bernilai negatif.');
                    }
                }
            ],
            'harga_jual'     => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ((float)$value < 0) {
                        $fail('Harga jual tidak boleh bernilai negatif.');
                    }
                }
            ],
            'tgl_kadaluarsa' => 'required|date',
        ], [
            'kode_obat.required'      => 'Kode obat wajib diisi.',
            'kode_obat.unique'        => 'Kode obat sudah digunakan.',
            'nama.required'           => 'Nama obat wajib diisi.',
            'nama.unique'             => 'Nama obat tersebut sudah digunakan oleh data obat lain.',
            'id_kategori.required'    => 'Kategori wajib dipilih.',
            'satuan.required'         => 'Satuan wajib diisi.',
            'tgl_kadaluarsa.required' => 'Tanggal kadaluarsa wajib diisi.',
        ]);

        // Pengaman manual tambahan untuk mendeteksi bypass form update/edit utama
        if ((int)$request->stok < 0) {
            return redirect()->back()->withInput()->with('error', 'Gagal Memperbarui! Stok obat tidak boleh minus.');
        }

        if ((float)$request->harga_beli < 0) {
            return redirect()->back()->withInput()->with('error', 'Gagal Memperbarui! Harga beli tidak boleh minus.');
        }

        if ((float)$request->harga_jual < 0) {
            return redirect()->back()->withInput()->with('error', 'Gagal Memperbarui! Harga jual tidak boleh minus.');
        }

        $data = $request->all();
        $data['kategori_id'] = $request->id_kategori; 
        $data['stok'] = (int)$request->stok;
        $data['harga_beli'] = (float)$request->harga_beli;
        $data['harga_jual'] = (float)$request->harga_jual;
        
        $obat->update($data);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui!');
    }

    // 5.b Memproses Aksi Jendela Pop-up Modal "Tambah Stok"
    public function tambahStok(Request $request, string $id)
    {
        $request->validate([
            'jumlah_tambah' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ((int)$value <= 0) {
                        $fail('Gagal! Jumlah penambahan stok harus di atas angka 0 (tidak boleh 0 atau minus).');
                    }
                }
            ]
        ], [
            'jumlah_tambah.required' => 'Jumlah penambahan stok wajib diisi.',
        ]);

        if ((int)$request->jumlah_tambah <= 0) {
            return redirect()->back()
                ->with('error', 'Gagal Menambahkan Stok! Jumlah penambahan harus berupa angka positif di atas 0.');
        }

        $obat = Obat::findOrFail($id);
        
        $obat->stok += (int)$request->jumlah_tambah;
        $obat->save();

        return redirect()->route('obat.index')->with('success', "Stok untuk obat {$obat->nama} berhasil ditambahkan!");
    }

    // 6. Menghapus Obat (TC_OBAT_006)
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
        ], [
            'file.required' => 'File lampiran excel wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx atau .xls'
        ]);

        try {
            Excel::import(new ObatImport, $request->file('file'));
            return redirect()->route('obat.index')->with('success', 'Data obat berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('obat.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
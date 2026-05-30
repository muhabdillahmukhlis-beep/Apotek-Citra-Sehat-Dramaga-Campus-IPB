<p align="center">
  <h1 align="center">Apotek Citra Sehat - Dramaga Campus IPB</h1>
</p>

## Deskripsi Proyek
Sistem Informasi Manajemen Apotek yang dirancang untuk memudahkan pengelolaan stok obat dan transaksi penjualan. Proyek ini dibangun menggunakan Framework Laravel sebagai bagian dari tugas mata kuliah Rekayasa Perangkat Lunak (RPL).

## Fitur Utama
* **Dashboard Interaktif**: Ringkasan penjualan hari ini, total transaksi, dan grafik mingguan.
* **Manajemen Stok**: Notifikasi otomatis untuk stok yang menipis dan obat yang hampir kadaluarsa.
* **Transaksi Real-time**: Pencatatan penjualan obat dengan riwayat transaksi yang rapi.
* **Monitoring**: Daftar obat berdasarkan status stok dan tanggal kadaluarsa.

## Cara Instalasi di Lokal
Jika Anda ingin menjalankan proyek ini di komputer Anda, ikuti langkah-langkah berikut:

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/muhabdillahmukhlis-beep/Apotek-Citra-Sehat-Dramaga-Campus-IPB.git](https://github.com/muhabdillahmukhlis-beep/Apotek-Citra-Sehat-Dramaga-Campus-IPB.git)
    ```
2.  **Masuk ke Folder Proyek**
    ```bash
    cd Apotek-Citra-Sehat-Dramaga-Campus-IPB
    ```
3.  **Install Dependency**
    ```bash
    composer install
    npm install && npm run dev
    ```
4.  **Konfigurasi Environment**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.
    ```bash
    cp .env.example .env
    ```
5.  **Generate App Key & Migrate**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
6.  **Jalankan Server**
    ```bash
    php artisan serve
    ```

7. **gunakan Username : admin**
    **Sandi : admin1234**

---
**Tujuan:** Proyek Semester 4 - IPB University

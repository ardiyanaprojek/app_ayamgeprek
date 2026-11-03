# Spesifikasi Sistem POS & Inventaris Warung Ayam Geprek

**Target Pengguna:** Pemilik Tunggal (*Single Account / Owner-Operated*)  
**Metode Pembayaran:** 100% Tunai (*Cash Only*)  
**Mekanisme Struk:** Manual / Atas Permintaan Pelanggan (*On-Demand Printing*)  
**Fokus Sistem:** Autentikasi Pengguna, Konfigurasi Profil Toko, Kasir Kilat, Cetak Struk Opsional (58mm), Inventaris Bahan Pokok Harian, dan Rekap Laci Kas.
**responsif:** mobile dan dekstop

---

## 1. Modul Fitur Berdasarkan Kategori

### 1.1 Modul Akun & Keamanan (Autentikasi)
* **Login Sistem:**
  * Masuk menggunakan kombinasi `Username` dan `Password`.
  * Fitur sesi aktif (*Remember Me*) agar tidak perlu login berulang kali selama jam operasional warung.
* **Kelola Profil Akun:**
  * Formulir ubah `Username`.
  * Formulir ganti `Password` (wajib memasukkan password lama untuk verifikasi keamanan sebelum menyimpan password baru).
* **Fungsi Logout:**
  * Tombol keluar aman untuk mengunci akses sistem saat perangkat ditinggalkan.

### 1.2 Modul Konfigurasi Toko & Perangkat (Settings)
* **Profil Outlet (Data Header Struk):**
  * Nama Warung / Usaha (misal: *Warung Ayam Geprek Sambal Juara*).
  * Alamat Lengkap Toko.
  * Nomor Telepon / WhatsApp (kontak pemesanan).
  * Pesan Penutup Struk / *Footer Note* (misal: *"Terima kasih! Menerima pesanan katering"*).
* **Pengaturan Cetak (Printer Thermal):**
  * Mode koneksi printer: Bluetooth atau USB.
  * Ukuran lebar kertas: Standar `58mm` atau `80mm`.
  * Status cetak bawaan: **Manual/Opsional** (hemat kertas, struk hanya dicetak jika pembeli meminta).

### 1.3 Modul Data Master (Pengaturan Katalog & Bahan)
* **Master Bahan Baku Utama:**
  * Pendaftaran bahan pokok mentah: *Ayam Potong*, *Usus Porsi*, *Telur*, *Tahu/Tempe*.
  * Satuan hitung pasti dan bulat: `potong`, `porsi`, `butir`, `biji` (tanpa perhitungan gram bumbu/tepung/minyak).
  * Batas stok minimum sebagai sinyal belanja pasar.
* **Master Menu Jualan:**
  * Input nama menu (contoh: *Ayam Geprek Dada*, *Usus Crispy*, *Es Teh Manis*) dan harga jual per porsi.
  * Menghubungkan menu dengan bahan pokok utama:
    * 1 porsi menu ayam memotong 1 potong *Ayam Potong*.
    * 1 porsi menu usus memotong 1 porsi *Usus Porsi*.
    * Menu minuman / nasi tambahan diset `NULL` (tidak memotong stok bahan baku).

### 1.4 Modul Inventaris & Stok Harian
* **Siklus Stok Otomatis Antar-Hari:**
  * Sisa fisik pada malam hari saat tutup warung otomatis menjadi **stok awal** keesokan paginya.
  * Penjual **tidak perlu** menginput ulang stok dari nol setiap pagi.
* **Input Belanja Baru (Stok Masuk Pagi/Siang):**
  * Formulir cepat satu klik: masukkan jumlah bahan yang baru dibeli dari pasar (misal: belanja masuk 50 potong ayam).
* **Penyesuaian Tutup Toko (Stok Opname Ringkas Malam):**
  * Di akhir jam operasional, penjual mengecek fisik chiller.
  * Masukkan sisa fisik yang layak pakai untuk esok hari.
  * Masukkan jumlah bahan rusak/basi/gosong jika ada, sehingga sistem mencatat selisih fisik secara akurat.

### 1.5 Modul Kasir & Transaksi (POS)
* **Proses Transaksi Kilat:**
  * Tampilan grid kartu menu jualan responsif.
  * Kalkulasi total belanja dan subtotal otomatis.
  * Input nominal uang tunai diterima atau tombol cepat uang pas (Rp 10.000, Rp 20.000, Rp 50.000, Rp 100.000).
  * Kalkulasi dan penayangan nominal uang kembalian secara presisi.
* **Pengurangan Stok Otomatis:**
  * Setiap transaksi berhasil diselesaikan, stok bahan utama terkait langsung berkurang otomatis di database.
* **Alur Selesai Transaksi & Cetak Manual:**
  * Setelah tombol **Bayar** ditekan, muncul jendela popup kembalian beserta dua pilihan tombol:
    1. **Selesai Tanpa Struk:** Transaksi tersimpan, kasir langsung siap melayani antrean berikutnya (menghemat kertas).
    2. **Cetak Struk:** Mengirim sinyal cetak ke thermal printer hanya jika pembeli meminta nota.

### 1.6 Modul Laporan & Tutup Buku Harian
* **Rekap Kas Harian (Uang di Laci):**
  * Total omzet tunai harian.
  * Total jumlah transaksi yang tercatat.
  * Angka laporan kasir harus cocok 100% dengan lembaran uang riil di laci kasir.
* **Rekap Penggunaan Bahan Pokok:**
  * Formula pergerakan bahan harian:
    $$\text{Stok Awal} + \text{Belanja Masuk} - \text{Terjual Kasir} - \text{Rusak/Basi} = \text{Sisa Siap Pakai Besok}$$

---

## 2. Struktur Database Lengkap

### 2.1 Tabel `users` (Akun Pengguna)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Auto) | ID akun pemilik |
| `username` | VARCHAR(50) (Unique) | Username untuk masuk ke sistem |
| `password_hash` | VARCHAR(255) | Hash kata sandi terenkripsi (bcrypt/argon2) |
| `nama_lengkap` | VARCHAR(100) | Nama panggilan pemilik |
| `updated_at` | TIMESTAMP | Waktu terakhir akun diperbarui |

### 2.2 Tabel `store_settings` (Konfigurasi Profil Toko & Alat)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Default 1) | Baris konfigurasi tunggal (*singleton*) |
| `nama_toko` | VARCHAR(120) | Nama gerai yang muncul di struk |
| `alamat` | TEXT | Alamat fisik gerai |
| `no_telepon` | VARCHAR(25) | Nomor HP / WhatsApp |
| `pesan_footer` | VARCHAR(255) | Catatan penutup di bawah struk |
| `printer_paper` | VARCHAR(10) (Default '58mm') | Ukuran kertas printer ('58mm' atau '80mm') |
| `updated_at` | TIMESTAMP | Waktu konfigurasi terakhir diubah |

### 2.3 Tabel `ingredients` (Bahan Pokok)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Auto) | ID bahan baku |
| `nama_bahan` | VARCHAR(100) | Contoh: `Ayam Potong`, `Usus Porsi` |
| `satuan` | VARCHAR(20) | `potong`, `porsi`, `butir`, `biji` |
| `stok_saat_ini` | INTEGER | Jumlah fisik bahan tersedia |
| `stok_minimum` | INTEGER | Peringatan jika stok menipis |

### 2.4 Tabel `products` (Katalog Menu Jualan)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Auto) | ID menu jualan |
| `nama_menu` | VARCHAR(120) | Nama item menu |
| `harga_jual` | DECIMAL(10, 2) | Harga jual per porsi |
| `ingredient_id` | INTEGER (FK, Nullable) | Terhubung ke tabel `ingredients` |
| `jumlah_potong` | INTEGER (Default 1) | Jumlah bahan terpotong per porsi menu |

### 2.5 Tabel `stock_logs` (Riwayat Keluar Masuk Bahan)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Auto) | ID mutasi stok |
| `ingredient_id` | INTEGER (FK) | ID bahan pokok |
| `tipe` | ENUM('masuk', 'penjualan', 'rusak_basi') | Jenis pergerakan stok |
| `jumlah` | INTEGER | Nilai mutasi (+ masuk, - keluar) |
| `tanggal` | DATE | Tanggal operasional |
| `keterangan` | VARCHAR(255) | Catatan (misal: "Belanja pagi", "2 potong basi") |

### 2.6 Tabel `transactions` (Header Transaksi Kasir)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Auto) | ID transaksi |
| `nomor_struk` | VARCHAR(50) (Unique) | Nomor nota (contoh: `NOTA-20261103-001`) |
| `total_bayar` | DECIMAL(10, 2) | Total harga pesanan |
| `nominal_diterima` | DECIMAL(10, 2) | Uang tunai yang diserahkan pelanggan |
| `kembalian` | DECIMAL(10, 2) | Uang kembalian yang diberikan |
| `metode_bayar` | VARCHAR(10) (Default 'TUNAI') | Selalu 'TUNAI' |
| `created_at` | TIMESTAMP | Tanggal dan jam transaksi |

### 2.7 Tabel `transaction_items` (Rincian Menu Dipesan)
| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | INTEGER (PK, Auto) | ID rincian item |
| `transaction_id` | INTEGER (FK) | Terhubung ke tabel `transactions` |
| `product_id` | INTEGER (FK) | Terhubung ke tabel `products` |
| `qty` | INTEGER | Jumlah porsi dipesan |
| `harga_satuan` | DECIMAL(10, 2) | Harga jual saat transaksi terjadi |
| `subtotal` | DECIMAL(10, 2) | `qty * harga_satuan` |
| `catatan` | VARCHAR(100) | Catatan pesanan (misal: "Pedas sedang") |

---

## 3. Format Struk Kasir (Dicetak Sesuai Permintaan)

```
================================
     [store_settings.nama_toko]       
   [store_settings.alamat]   
    Telp: [store_settings.no_telepon]     
================================
No. Struk : NOTA-20261103-0014
Waktu     : 03-11-2026 14:10
--------------------------------
Ayam Geprek Dada
  2 porsi x Rp 15.000   Rp 30.000
  * Level 5 pedas

Usus Crispy Geprek
  1 porsi x Rp 12.000   Rp 12.000

Es Teh Manis
  2 gelas x Rp  4.000   Rp  8.000
--------------------------------
TOTAL                   Rp 50.000
TUNAI                   Rp 50.000
Kembalian               Rp      0
================================
  [store_settings.pesan_footer]
================================
```

---

## 4. Format Laporan Penjualan & Bahan Harian

### 4.1 Rekap Kasir (Laci Kas)
* **Tanggal:** 03 November 2026
* **Total Transaksi Terlayani:** 35 Transaksi
* **Total Penjualan Tunai:** **Rp 1.120.000** *(Wajib cocok 100% dengan total uang fisik di laci kasir)*

### 4.2 Rekap Bahan Pokok
| Bahan Baku | Stok Awal | Masuk (Belanja) | Terjual (Kasir) | Rusak / Basi | Sisa Akhir (Fisik) |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Ayam Potong** | 8 | +50 | -48 | -2 | **8 potong** |
| **Usus Porsi** | 4 | +20 | -19 | 0 | **5 porsi** |
| **Telur** | 10 | +30 | -25 | 0 | **15 butir** |

*(Angka pada kolom **Sisa Akhir** otomatis menjadi Stok Awal untuk esok hari).*
# Spesifikasi Desain Antarmuka (UI/UX) & Panduan Implementasi Frontend
## Sistem POS & Inventaris Warung Ayam Geprek

Dokumen ini merupakan panduan implementasi visual antarmuka web yang menggabungkan estetika **Modern Clean Dashboard** (referensi: *sidebar navigasi vertikal, global search bar, card container rounded dengan soft border, multi-filter toolbar, badge status terstandarisasi, dan pagination footer*) dengan fungsionalitas operasional kasir & inventaris dari **PRD.md**.

Dokumen ini disusun sebagai acuan teknis dan **Master Prompt GitHub Copilot** menggunakan **Bootstrap 5.3** dan **CSS Murni (Custom CSS)** tanpa library eksternal yang memberatkan, sepenuhnya responsif (*mobile & desktop*).

---

## 1. Analisis Referensi Visual & Penyesuaian PRD

Berdasarkan referensi visual dashboard:
1. **Navigasi Kiri (Vertical Sidebar):**
   - Brand logo di area atas (`Ayam Geprek POS`).
   - Pengelompokan menu secara logis: Bagian Utama (`Dashboard/Kasir`, `Katalog Menu`, `Bahan Baku`, `Mutasi Stok`), Bagian Laporan (`Rekap Kasir & Bahan`), dan Bagian Pengaturan (`Profil Warung & Printer`).
   - Indikator menu aktif berbentuk *pill tab* kontras dengan teks kontras tinggi.
2. **Top Header Bar:**
   - Input pencarian global dengan shortcut visual hint (misal: `Ctrl + K` / `⌘ K`).
   - Quick action button / status koneksi printer thermal (58mm/80mm).
   - Profil akun pengguna (`Single Account / Owner-Operated`) di pojok kanan atas dengan avatar dan status aktif.
3. **Workspace & Filter Toolbar:**
   - Toolbar fleksibel: Search box instan, dropdown filter kategori status, sorting order, dan tombol utama **CTA (+ Tambah Data / Transaksi Baru)** di sisi kanan.
4. **Data Presentation (Tabel Desktop & Kartu Mobile):**
   - Baris data rapat (*high information density*) dengan baris hover lembut.
   - Angka stok dan nominal uang wajib sejajar vertikal (*tabular figures*).
   - Status badge semantik: Aman (Hijau), Menipis (Kuning/Oranye), Habis (Merah).
   - Tombol aksi ringkas: Tombol `Edit` (ikon/outline) dan `Hapus` (outline danger) dengan modal konfirmasi.
5. **Footer Pagination:**
   - Navigasi halaman yang rapi dan konsisten di bagian bawah container card data.

---

## 2. Sistem Warna, Tipografi & Desain Token (Anti-AI Slop)

Sesuai kaidah desain antarmuka manusiawi operasional nyata, palet warna menerapkan rasio **60-30-10** (menghindari gradasi neon ungu/cyan generic AI).

### 2.1 Palet Warna (Color Palette)
| Token UI | Class / Variable | Nilai Hex | Fungsi & Penerapan |
| :--- | :--- | :--- | :--- |
| **Canvas Background (60%)** | `--pos-bg-body` / `bg-light` | `#f8fafc` | Latar belakang kanvas aplikasi, ramah mata kasir. |
| **Surface Card & Sidebar (30%)** | `--pos-bg-surface` / `bg-white` | `#ffffff` | Background kartu, sidebar, dan modal form. |
| **Border & Divider** | `--pos-border` | `#e2e8f0` | Garis batas tipis 1px yang tegas dan bersih. |
| **Brand Accent / Primary CTA (10%)** | `.btn-brand` / `--pos-brand` | `#e11d48` / `#be123c` | Warna brand ayam geprek (Rose Red tegas) untuk CTA utama. |
| **Active Nav Highlight** | `.nav-active` | `#6366f1` / `#4f46e5` | Aksen warna aktif navigasi dashboard (referensi gambar). |
| **Text Primary** | `text-slate-900` | `#0f172a` | Judul, label tabel, nominal harga. |
| **Text Secondary** | `text-slate-500` | `#64748b` | Satuan, SKU/ID nota, tanggal, helper text. |

### 2.2 Status Semantik Stok & Pembayaran
* **Stok Aman / Transaksi Berhasil:**
  - Class: `badge-status-safe` (`background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;`)
* **Stok Menipis:**
  - Class: `badge-status-warning` (`background: #fef9c3; color: #a16207; border: 1px solid #fde047;`)
* **Stok Habis / Rusak-Basi:**
  - Class: `badge-status-danger` (`background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3;`)

### 2.3 Tipografi
* **Font Family:** `'Plus Jakarta Sans', system-ui, -apple-system, sans-serif`
* **Tabular Numbers:** Wajib `font-variant-numeric: tabular-nums;` untuk semua elemen harga uang (Rp), subtotal, kembalian laci, dan kuantitas stok bahan.

---

## 3. Tata Letak Arsitektur Responsif (Layout Architecture)

### 3.1 Layar Desktop ($\ge 992\text{px}$)
* **Sidebar Kiri Tetap (Fixed / Sticky Sidebar):** Lebar 260px, posisi vertikal penuh.
* **Area Kerja Kanan (Main Content):**
  - **Header Nav:** Tinggi 70px dengan background putih dan border-bottom halus.
  - **Sub-Header:** Ringkasan KPI Cards (Total Omzet Kasir Harian, Sisa Porsi Ayam Hari Ini, Transaksi Terlayani).
  - **Data Card Area:** Tabel Bootstrap dengan wrapper `table-responsive`, toolbar aksi di atas, dan pagination di bawah.

### 3.2 Layar Mobile ($< 992\text{px}$)
* **Offcanvas Sidebar:** Sidebar disembunyikan dan dapat dibuka lewat tombol hamburger di header atas.
* **Transformasi Data (Table to Cards):**
  - Tabel desktop disembunyikan menggunakan utilitas `.d-none .d-md-table`.
  - Data ditampilkan dalam format list kartu vertikal (`.d-block .d-md-none`) yang nyaman disentuh jempol (*thumb-friendly*).
* **Modal Dialog & Kasir:** Menyesuaikan lebar layar ponsel (*bottom sheet* atau modal terpusat *fullscreen* pada layar kecil).

---

## 4. Penyesuaian Fitur Khusus Berdasarkan PRD.md

1. **Modul Kasir Kilat (POS 100% Tunai):**
   - Grid kartu menu jualan dengan harga per porsi dan tombol tambah cepat.
   - Ringkasan keranjang pesanan (*order summary*) di sisi kanan (desktop) atau floating bar (mobile).
   - Tombol nominal uang pas cepat: `Rp 10.000`, `Rp 20.000`, `Rp 50.000`, `Rp 100.000`.
   - Pop-up modal kembalian dengan dua tombol tindakan jelas:
     * **Selesai Tanpa Struk** (Aksi default untuk efisiensi kertas).
     * **Cetak Struk (58mm/80mm)** (Hanya saat pembeli meminta nota).
2. **Modul Inventaris & Stok Harian:**
   - Tabel mutasi stok: Bahan baku, stok awal, barang masuk pasar (+), terjual kasir (-), rusak/basi (-), dan sisa siap pakai esok hari.
3. **Modul Tutup Buku & Rekap Laci Kasir:**
   - Kartu validasi kas fisik yang membandingkan total transaksi kasir dengan uang tunai riil di laci.
4. **Modul Konfigurasi Outlet & Cetak:**
   - Form data header/footer struk dan toggle ukuran kertas printer.

---

## 5. File Template Induk CSS & Bootstrap

Berikut adalah blok styling murni (CSS kustom) pelengkap Bootstrap 5.3 yang mengadopsi gaya visual dashboard referensi:

```css
/* ========================================================
   CUSTOM STYLING: WARUNG POS & INVENTARIS (Bootstrap 5.3)
   ======================================================== */

:root {
  --pos-primary: #6366f1;
  --pos-primary-hover: #4f46e5;
  --pos-brand-red: #e11d48;
  --pos-brand-red-hover: #be123c;
  --pos-bg-body: #f8fafc;
  --pos-card-border: #f1f5f9;
  --pos-text-main: #1e293b;
  --pos-text-muted: #64748b;
  --pos-radius-main: 12px;
  --pos-radius-sm: 8px;
}

body {
  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
  background-color: var(--pos-bg-body);
  color: var(--pos-text-main);
  -webkit-font-smoothing: antialiased;
}

/* Tabular numbers for financial and stock figures */
.font-numeric, 
.table td, 
.pos-price-tag, 
.badge-count {
  font-variant-numeric: tabular-nums;
  letter-spacing: -0.01em;
}

/* Sidebar styling */
.pos-sidebar {
  width: 260px;
  background-color: #ffffff;
  border-right: 1px solid var(--pos-card-border);
  min-height: 100vh;
}

.pos-sidebar .nav-link {
  color: var(--pos-text-muted);
  font-weight: 500;
  padding: 10px 16px;
  border-radius: var(--pos-radius-sm);
  display: flex;
  align-items: center;
  gap: 12px;
  transition: all 0.15s ease-in-out;
}

.pos-sidebar .nav-link:hover {
  background-color: #f8fafc;
  color: var(--pos-text-main);
}

.pos-sidebar .nav-link.active {
  background-color: var(--pos-primary);
  color: #ffffff;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

/* Section Header in Sidebar */
.pos-sidebar-heading {
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #94a3b8;
  font-weight: 700;
  padding: 16px 16px 8px;
}

/* Search Input Global */
.pos-search-input {
  background-color: #f8fafc;
  border: 1px solid var(--pos-card-border);
  border-radius: 20px;
  padding: 8px 16px 8px 40px;
  font-size: 0.875rem;
}

.pos-search-input:focus {
  background-color: #ffffff;
  border-color: #cbd5e1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Card Container Modern */
.pos-card {
  background: #ffffff;
  border: 1px solid #f1f5f9;
  border-radius: var(--pos-radius-main);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
}

/* Table Style matching reference */
.pos-table th {
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: capitalize;
  color: #64748b;
  border-bottom: 1px solid #f1f5f9;
  padding: 14px 16px;
  background-color: #ffffff;
}

.pos-table td {
  padding: 14px 16px;
  font-size: 0.875rem;
  border-bottom: 1px solid #f8fafc;
  vertical-align: middle;
}

.pos-table tbody tr:hover {
  background-color: #fafbfd;
}

/* Action Button Style */
.btn-pos-primary {
  background-color: var(--pos-primary);
  border-color: var(--pos-primary);
  color: #ffffff;
  border-radius: var(--pos-radius-sm);
  font-weight: 600;
  padding: 8px 16px;
}

.btn-pos-primary:hover {
  background-color: var(--pos-primary-hover);
  border-color: var(--pos-primary-hover);
  color: #ffffff;
}

.btn-pos-edit {
  background-color: #ede9fe;
  color: #6366f1;
  border: none;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 6px 12px;
}

.btn-pos-edit:hover {
  background-color: #ddd6fe;
  color: #4f46e5;
}

.btn-pos-delete {
  background-color: transparent;
  color: #94a3b8;
  border: none;
  padding: 6px 8px;
  border-radius: 6px;
}

.btn-pos-delete:hover {
  color: #ef4444;
  background-color: #fee2e2;
}

/* Semantic Badges */
.pos-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.775rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
}

.pos-badge-safe {
  background-color: #ecfdf5;
  color: #059669;
}
.pos-badge-safe::before {
  content: "";
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #10b981;
}

.pos-badge-warning {
  background-color: #fffbeb;
  color: #d97706;
}
.pos-badge-warning::before {
  content: "";
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #f59e0b;
}

.pos-badge-danger {
  background-color: #fff1f2;
  color: #e11d48;
}
.pos-badge-danger::before {
  content: "";
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #f43f5e;
}

/* Custom Pagination */
.pos-pagination .page-link {
  border: none;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 6px;
  margin: 0 2px;
}

.pos-pagination .page-item.active .page-link {
  background-color: #ede9fe;
  color: #6366f1;
  font-weight: 700;
}
```

---

## 6. Template Prompt Siap Salin untuk GitHub Copilot

Gunakan prompt di bawah ini langsung di obrolan **GitHub Copilot Chat** (VS Code) untuk mengimplementasikan atau memperbarui view Blade / HTML aplikasi.

### Master Prompt Copilot:
```text
Bertindaklah sebagai Senior Frontend UI/UX Developer spesialis Laravel Blade dan Bootstrap 5.3.

Bantu saya membuat/merefaktor template antarmuka untuk aplikasi "Warung POS & Inventaris Ayam Geprek" dengan mengadopsi tata letak modern dashboard profesional dari gambar referensi:
1. Sidebar vertikal tetap di kiri dengan grup menu (Kasir POS, Katalog Menu, Bahan Baku, Stok Harian, Laporan Kasir, Konfigurasi Struk).
2. Header atas berisi global search input dengan icon magnifier, indikator status printer Bluetooth 58mm, dan user avatar profile "Pemilik Warung".
3. Layout card kontainer utama (pos-card) berwarna putih dengan border halus 1px (#f1f5f9) dan rounded corner 12px.
4. Filter Toolbar di atas tabel data (Search box, Filter Dropdown, Sort Dropdown, dan Tombol CTA Utama + Tambah Data).
5. Desain responsif dua mode:
   - Desktop (>= 992px): Tabel data rapi dengan font-numeric (tabular-nums), kolom checkbox, thumbnail menu/bahan, nama item, harga satuan/stok, views/porsi, badge status aktif/aman, serta tombol aksi Edit (soft purple) dan Hapus (icon).
   - Mobile (< 992px): Tabel berganti otomatis menjadi kartu vertikal yang thumb-friendly, sidebar berubah menjadi offcanvas Bootstrap.
6. Footer pagination dengan nomor halaman aktif dan tombol navigasi prev/next rapi.

Gunakan framework Bootstrap 5.3 dan Custom CSS murni yang ringkas (tanpa library JavaScript berat tambahan). Pastikan semua teks nominal uang dan angka stok menggunakan font tabular nums yang sejajar secara vertikal.
```
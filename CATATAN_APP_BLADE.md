# Catatan Belajar `app.blade.php`

File [app.blade.php](resources/views/layouts/app.blade.php) adalah **layout utama** aplikasi. Isinya adalah kerangka halaman yang digunakan bersama oleh halaman lain, seperti Dashboard dan Master Data Bahan Baku. Dengan layout, elemen umum tidak perlu ditulis ulang di setiap halaman.

## Gambaran alur

1. Browser meminta halaman, lalu Laravel memilih sebuah view.
2. View halaman menggunakan layout ini dengan `@extends('layouts.app')`.
3. Layout menyiapkan struktur HTML, aset CSS/JavaScript, sidebar, header, dan navigasi mobile.
4. Isi khusus halaman ditampilkan pada `@yield('content')`.

Contoh penggunaan dari halaman:

```blade
@extends('layouts.app')

@section('title', 'Nama Halaman')

@section('content')
    <h1>Konten halaman</h1>
@endsection
```

## Bagian-bagian file

### 1. Deklarasi dokumen dan bahasa

```html
<!DOCTYPE html>
<html lang="id">
```

`<!DOCTYPE html>` memberi tahu browser bahwa dokumen memakai HTML5. Atribut `lang="id"` menyatakan bahasa halaman adalah bahasa Indonesia, yang membantu aksesibilitas dan alat bantu browser.

### 2. Informasi halaman di `<head>`

```html
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0f172a">
<title>@yield('title', 'Dashboard') - Ayam Geprek Istiqomah</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

- `charset="utf-8"` mendukung karakter seperti `＋`, `◷`, dan huruf beraksen.
- `viewport` membuat lebar halaman mengikuti lebar perangkat, penting untuk tampilan mobile.
- `theme-color` memberi warna tema pada browser/perangkat yang mendukungnya.
- `@yield('title', 'Dashboard')` menyediakan judul yang dapat diisi halaman. Jika halaman tidak mengisi `title`, nilai bawaan adalah `Dashboard`.
- `@vite(...)` memasukkan CSS dan JavaScript melalui Vite. Dalam proyek ini CSS mengimpor Bootstrap, sedangkan JavaScript menangani interaksi.

### 3. Pembungkus aplikasi dan latar sidebar

```blade
<div class="app-shell">
    <button class="sidebar-backdrop" ... data-sidebar-close></button>
```

`app-shell` membungkus seluruh aplikasi. Tombol `sidebar-backdrop` adalah lapisan gelap di belakang sidebar pada tampilan kecil. JavaScript menggunakan atribut `data-sidebar-close` untuk menutup sidebar ketika lapisan ini ditekan.

### 4. Sidebar

```blade
<aside class="app-sidebar bg-dark text-white p-3" id="app-sidebar">
```

`<aside>` menampung navigasi samping dan informasi akun. `id="app-sidebar"` menjadi pengenal bagi tombol hamburger. Kelas seperti `bg-dark`, `text-white`, dan `p-3` adalah utility Bootstrap untuk warna dan jarak.

Di dalam sidebar:

- **Brand** menampilkan inisial dan nama warung. Link brand mengarah ke `route('dashboard')`.
- **Navigasi utama** berisi link ke bagian aplikasi.
- **Footer sidebar** menampilkan tombol Tutup Warung dan profil pemilik.

#### Menandai menu aktif

```blade
{{ request()->routeIs('ingredients.*') ? 'active' : '' }}
```

Ini adalah ekspresi Blade/PHP. Laravel memeriksa apakah route saat ini cocok dengan pola `ingredients.*`. Jika cocok, kelas `active` ditambahkan sehingga menu dapat diberi gaya aktif.

```blade
@if (request()->routeIs('ingredients.*')) aria-current="page" @endif
```

`aria-current="page"` memberi tahu teknologi bantu bahwa link tersebut menunjuk ke halaman yang sedang dibuka.

#### Route dan link sementara

- `route('ingredients.index')` membuat URL dari nama route Laravel, bukan menulis URL secara langsung.
- Link seperti `href="#stok"` melompat ke elemen dengan `id="stok"` di halaman yang sama. Link ini belum berpindah ke halaman fitur tersendiri. Jika fitur sudah memiliki route, ganti dengan `route('nama.route')`.

### 5. Tombol hamburger

```blade
<button ... aria-controls="app-sidebar" aria-expanded="false" data-sidebar-toggle>
```

Tombol ini ditampilkan di layar kecil menggunakan `d-lg-none`. JavaScript mencari `data-sidebar-toggle` untuk membuka atau menutup sidebar.

- `aria-controls` menghubungkan tombol dengan sidebar yang dikendalikannya.
- `aria-expanded` menyatakan status buka/tutup dan diperbarui oleh JavaScript.
- Tiga atribut `data-*` di layout adalah kait bagi JavaScript, bukan kelas Bootstrap.

### 6. Header / topbar

```blade
<header class="navbar bg-white border-bottom sticky-top ...">
```

Header menampilkan nama warung, tanggal/jam, dan status operasional.

- `sticky-top` adalah utility Bootstrap agar header tetap berada di atas saat halaman digulir.
- `d-lg-none` menampilkan elemen hanya di bawah breakpoint `lg`.
- `d-none d-sm-inline` menyembunyikan tanggal pada layar sangat kecil, lalu menampilkannya mulai breakpoint `sm`.
- `now()` adalah helper Laravel untuk waktu saat ini.
- `format('d M Y, H:i')` memformat waktu, contohnya `03 Nov 2026, 21:00`.
- `datetime="{{ now()->toIso8601String() }}"` menyediakan waktu standar untuk mesin; isi `<time>` menyediakan format yang mudah dibaca.

Nama warung dan lokasi saat ini ditulis langsung sebagai teks di Blade. Jika nantinya berasal dari konfigurasi toko, bagian ini dapat diganti dengan data dari controller.

### 7. Area konten halaman

```blade
<main class="container-fluid py-4 px-3 px-md-4">
    @yield('content')
</main>
```

`<main>` menandai konten utama halaman. `@yield('content')` adalah tempat Blade menyisipkan bagian `@section('content')` dari view anak.

Kelas Bootstrap mengatur tata letak:

- `container-fluid`: area konten selebar ruang yang tersedia.
- `py-4`: jarak vertikal atas dan bawah.
- `px-3 px-md-4`: padding horizontal lebih besar mulai breakpoint `md`.

### 8. Navigasi bawah mobile

```blade
<nav class="mobile-bottom-nav d-lg-none" aria-label="Navigasi bawah">
```

Navigasi ini berisi akses cepat di bagian bawah layar kecil. `d-lg-none` menyembunyikannya pada ukuran desktop. `aria-label` memberi nama yang dapat dipahami pembaca layar.

Saat ini hanya menu Dashboard diberi kelas `active` secara tetap. Jika navigasi ini dipakai di banyak halaman, kelas aktif sebaiknya dibuat dinamis seperti menu sidebar menggunakan `request()->routeIs(...)`.

## Blade dan Bootstrap: bedanya

- **Blade** adalah sistem template Laravel: `@yield`, `@section`, `@if`, `{{ ... }}`, dan `route(...)`.
- **Bootstrap** menyediakan kelas tampilan seperti `d-flex`, `gap-3`, `bg-white`, `p-3`, dan `d-lg-none`.
- **JavaScript** mengatur perilaku interaktif seperti membuka sidebar.
- **CSS proyek** di `resources/css/app.css` mengatur detail khusus yang tidak disediakan langsung oleh utility Bootstrap.

## Catatan pengembangan

1. Buat halaman baru dengan `@extends('layouts.app')` agar struktur umum tetap konsisten.
2. Isi judul lewat `@section('title', 'Judul Halaman')`.
3. Masukkan konten halaman ke `@section('content')`.
4. Gunakan nama route Laravel untuk navigasi antarhalaman; gunakan `#id` hanya untuk bagian yang memang ada di halaman saat ini.
5. Untuk menu aktif, periksa route dengan `request()->routeIs(...)`.
6. Ketika mengubah `data-sidebar-toggle`, `data-sidebar-close`, atau `id="app-sidebar"`, periksa juga selector yang menggunakannya di `resources/js/app.js`.

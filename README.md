# Jahitly - Sistem Manajemen Penjahit

Jahitly adalah aplikasi berbasis web yang dirancang untuk mempermudah operasional dan manajemen bisnis jahit atau *tailor shop*. Sistem ini mencakup berbagai fitur lengkap mulai dari manajemen pesanan, jadwal fitting, inventaris bahan kain, hingga pelacakan pembayaran.

Aplikasi ini dibangun menggunakan teknologi modern dengan tumpukan **TALL Stack** (Tailwind CSS, Alpine.js, Laravel, dan Livewire) yang memastikan performa tinggi dan pengalaman pengguna (UX) yang interaktif.

## 🚀 Fitur Utama

- **Manajemen Pengguna (Role-based):** Sistem autentikasi untuk Admin dan Pelanggan.
- **Manajemen Pesanan (Order Management):** Melacak status pesanan secara real-time, melihat antrean, hingga pembatalan pesanan.
- **Penjadwalan Fitting (Appointments):** Fitur untuk mengatur jadwal pengukuran atau fitting baju dengan pelanggan.
- **Manajemen Bahan/Kain (Fabric Inventory):** Melacak stok kain (dalam meter) dan penggunaannya untuk setiap pesanan.
- **Manajemen Layanan:** Mengatur jenis layanan jahitan beserta detail harga.
- **Pelacakan Pembayaran:** Mencatat dan memonitor setiap transaksi pembayaran pesanan.
- **Manajemen File Desain:** Pelanggan atau admin dapat mengunggah gambar/file desain untuk referensi jahitan.
- **Testimoni & Notifikasi:** Pengelolaan ulasan dari pelanggan dan pemberitahuan pembaruan sistem.

## 🛠️ Tech Stack

- **Framework PHP:** [Laravel 13.x](https://laravel.com)
- **Frontend Framework:** [Livewire 4.x](https://livewire.laravel.com/)
- **CSS Framework:** [Tailwind CSS 3.x](https://tailwindcss.com/)
- **JavaScript Framework:** [Alpine.js](https://alpinejs.dev/)
- **Bundler:** [Vite](https://vitejs.dev/)
- **Database:** SQLite / MySQL (Dapat disesuaikan)

## 📋 Prasyarat

Pastikan sistem Anda telah menginstal kebutuhan berikut sebelum menjalankan proyek:

- PHP >= 8.3
- Composer
- Node.js & NPM

## ⚙️ Instalasi & Setup Proyek

Ikuti langkah-langkah di bawah ini untuk menjalankan Jahitly di komputer lokal Anda:

1. **Clone repositori ini** (atau pastikan Anda sudah berada di direktori proyek).
   ```bash
   cd jahitly
   ```

2. **Instal dependensi PHP melalui Composer**
   ```bash
   composer install
   ```

3. **Instal dependensi NPM dan build aset frontend**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Environment**
   Salin file konfigurasi bawaan Laravel:
   ```bash
   cp .env.example .env
   ```
   *Atur koneksi database Anda di dalam file `.env` (secara default Laravel menggunakan SQLite yang akan dibuat secara otomatis saat migrasi).*

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi Database**
   Perintah ini juga akan membuat file `database.sqlite` jika Anda menggunakan SQLite.
   ```bash
   php artisan migrate
   ```

7. **Jalankan Server Lokal**
   Gunakan perintah bawaan dari Composer script untuk menjalankan Laravel server dan Vite secara bersamaan:
   ```bash
   composer run dev
   ```
   Atau jalankan perintah ini pada terminal yang terpisah:
   ```bash
   php artisan serve
   ```
   ```bash
   npm run dev
   ```

8. **Akses Aplikasi**
   Buka browser Anda dan kunjungi: `http://localhost:8000`

## 📁 Struktur Utama Proyek

- `app/Models/` : Berisi representasi dari tabel di database (Order, Service, Fabric, Appointment, dll).
- `app/Livewire/` : Berisi komponen-komponen Livewire untuk logika frontend dinamis (contoh: `Admin\Appointments\Index.php`).
- `database/migrations/` : Berisi skema pembuatan tabel database aplikasi.
- `resources/views/` : Berisi template Blade dan tampilan komponen Livewire.
- `routes/` : Berisi definisi rute aplikasi (`web.php`, `auth.php`).

## 📄 Lisensi

Proyek ini dibuat untuk kebutuhan internal dan pengembangan. (Sesuaikan dengan lisensi proyek jika ada).

<div align="center">

# 🧵 Jahitly — Sistem Manajemen Penjahit

**Jahitly** adalah aplikasi manajemen bisnis jahit (*tailor shop*) berbasis web yang memudahkan penjahit dan pelanggan dalam memantau pesanan, jadwal fitting, pembayaran, hingga stok bahan — semua dalam satu platform yang modern dan responsif.

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-FB70A9?style=flat&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=flat&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat)](https://alpinejs.dev)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)

</div>

---

## ✨ Fitur Utama

### 👤 Role Pelanggan
| Fitur | Deskripsi |
|-------|-----------|
| **Daftar & Login** | Registrasi akun, login, reset kata sandi, verifikasi email |
| **Buat Pesanan** | Pemesanan jahitan (baru) atau vermak dengan pemilihan layanan, bahan, dan catatan desain |
| **Lacak Pesanan** | Pantau status produksi secara real-time (antrean → dijahit → selesai) beserta estimasi selesai |
| **Jadwal Fitting** | Buat janji konsultasi/pengukuran dengan penjahit |
| **Pembayaran** | Upload bukti pembayaran DP/pelunasan, lihat riwayat, dan unduh invoice PDF |
| **Profil** | Kelola data diri, foto profil, nomor WhatsApp, dan alamat |
| **Testimoni** | Beri rating dan ulasan setelah pesanan selesai |
| **Batalkan Pesanan** | Pembatalan pesanan dengan konfirmasi sebelum diproses |

### 🛠️ Role Admin
| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard** | Ringkasan bisnis — pesanan hari ini, pendapatan, pesanan aktif, dan statistik real-time |
| **Kelola Pesanan** | Lihat, filter, dan update status setiap pesanan pelanggan |
| **Antrian Produksi** | Kelola antrean jahitan dan perbarui progress produksi |
| **Verifikasi Pembayaran** | Review dan approve/tolak bukti pembayaran dari pelanggan |
| **Jadwal Fitting** | Kalender interaktif untuk konfirmasi dan manajemen janji temu |
| **Kelola Bahan/Kain** | Inventaris kain: tambah, edit, lacak stok (meter) dan status (ready/PO) |
| **Kelola Vermak** | Manajemen layanan vermak/perbaikan pakaian secara terpisah |
| **Login Admin** | Halaman login terpisah dengan portal admin yang aman |

---

## 🎨 Design System

Jahitly menggunakan sistem desain visual yang konsisten di seluruh halaman:

| Token | Nilai | Kegunaan |
|-------|-------|----------|
| `--color-ink` | `#1A1A2E` | Warna teks utama (navy gelap) |
| `--color-surface` | `#F7F5F2` | Background halaman (krem hangat) |
| `--color-primary` | `#2B4FFF` | Warna aksi utama (biru elektrik) |
| `--color-primary-hover` | `#1A3DE0` | Hover state primary |
| `--color-accent` | `#FF6B35` | Warna sorotan (oranye terakota) |
| `--color-muted` | `#6B7280` | Teks sekunder / placeholder |
| `--color-border` | `#E8E4DF` | Garis batas elemen |
| `--color-sidebar` | `#0F1729` | Background sidebar admin |

**Font:** `Plus Jakarta Sans` (body) + `Playfair Display` (heading/display)

**UI Pattern:** `rounded-2xl` / `rounded-3xl` untuk card, `rounded-full` untuk tombol pill, `rounded-xl` untuk input dan tombol form.

**Dark Mode:** Didukung penuh via kelas Tailwind (`dark:`) — toggle tersedia di sidebar admin.

---

## 🛠️ Tech Stack

| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| [Laravel](https://laravel.com) | 13.x | PHP Framework (backend, routing, auth, ORM) |
| [Livewire](https://livewire.laravel.com) | 4.x | Komponen frontend dinamis tanpa JavaScript manual |
| [Tailwind CSS](https://tailwindcss.com) | 3.x | Utility-first CSS framework |
| [Alpine.js](https://alpinejs.dev) | 3.x | JavaScript ringan untuk interaksi UI (modal, toggle, dsb) |
| [Vite](https://vitejs.dev) | — | Bundler & asset pipeline |
| MySQL | — | Database utama |
| PHP | 8.4 | Runtime |

---

## 📋 Prasyarat

Pastikan sistem Anda telah menginstal berikut:

- **PHP** >= 8.3
- **Composer** (manajemen dependensi PHP)
- **Node.js** >= 18 & **NPM**
- **MySQL** (atau MariaDB)
- **Git**

---

## ⚙️ Instalasi & Setup

```bash
# 1. Clone repositori
git clone https://github.com/fanmaull03/sistem_Jahitly.git
cd sistem_Jahitly

# 2. Instal dependensi PHP
composer install

# 3. Instal dependensi Node & build aset
npm install
npm run build

# 4. Konfigurasi environment
cp .env.example .env
```

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_jahitly
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 5. Generate application key
php artisan key:generate

# 6. Jalankan migrasi database (+ seeder jika ada)
php artisan migrate --seed

# 7. Buat symlink storage (untuk upload foto/file)
php artisan storage:link
```

---

## 🚀 Menjalankan Aplikasi Lokal

Jalankan dua terminal secara bersamaan:

**Terminal 1 — Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 — Vite Dev Server:**
```bash
npm run dev
```

Atau gunakan satu perintah:
```bash
composer run dev
```

Akses aplikasi di: **[http://localhost:8000](http://localhost:8000)**

| URL | Halaman |
|-----|---------|
| `/` | Landing Page |
| `/login` | Login Pelanggan |
| `/register` | Registrasi Pelanggan |
| `/admin/login` | Login Admin |
| `/orders` | Dashboard Pesanan Pelanggan |
| `/admin/dashboard` | Dashboard Admin |

---

## 📁 Struktur Utama Proyek

```
jahitly/
├── app/
│   ├── Enums/
│   │   ├── OrderStatus.php           # ✨ Type-safe enum untuk status pesanan
│   │   └── PaymentStatus.php         # ✨ Type-safe enum untuk status pembayaran
│   ├── Services/
│   │   ├── PaymentService.php                    # Service pembayaran (centralized)
│   │   ├── OrderBusinessRulesService.php        # Validasi & perhitungan bisnis
│   │   ├── OrderStatusTransitionService.php    # ✨ Phase 2: Manajemen status workflow
│   │   ├── OrderRejectionService.php           # ✨ Phase 2: Logika rejection pesanan
│   │   ├── OrderPricingService.php             # ✨ Phase 2: Manajemen DP & pricing
│   │   └── OrderMaterialService.php            # ✨ Phase 2: Manajemen bahan/material
│   ├── Livewire/
│   │   ├── Admin/
│   │   │   ├── Dashboard.php          # Ringkasan bisnis admin
│   │   │   ├── Orders/               # Kelola pesanan (✨ refactored Phase 2)
│   │   │   ├── Queue/                # Antrian produksi
│   │   │   ├── Payments/             # Verifikasi pembayaran
│   │   │   ├── Appointments/         # Jadwal fitting
│   │   │   ├── Fabrics/              # Inventaris bahan kain
│   │   │   └── Vermaks/              # Kelola vermak
│   │   └── Customer/
│   │       ├── Orders/               # Pesanan pelanggan (list, detail, buat, batal)
│   │       ├── Payments/             # Pembayaran & riwayat
│   │       └── Appointments/         # Buat janji fitting
│   ├── Models/
│   │   ├── User.php                  # Pelanggan & Admin (role-based)
│   │   ├── Order.php                 # Pesanan jahitan (✨ dengan cancel methods)
│   │   ├── Service.php               # Jenis layanan
│   │   ├── Fabric.php                # Inventaris bahan kain
│   │   ├── Appointment.php           # Jadwal fitting
│   │   ├── Payment.php               # Transaksi pembayaran
│   │   ├── Testimonial.php           # Ulasan pelanggan
│   │   ├── DesignFile.php            # File desain/referensi
│   │   ├── OrderStatusLog.php        # Log perubahan status pesanan
│   │   └── AlterationOption.php      # Pilihan vermak
│   ├── Http/
│   │   ├── Controllers/              # Route handlers
│   │   ├── Middleware/               # Auth middleware
│   │   │   ├── AdminMiddleware.php    # Guard route admin
│   │   │   └── CustomerMiddleware.php # Guard route pelanggan
│   │   └── Requests/                 # Form validation
│   ├── Notifications/
│   │   ├── OrderStatusUpdated.php    # Notifikasi update status order
│   │   ├── PaymentStatusUpdated.php  # Notifikasi status pembayaran
│   │   └── NewTestimonial.php        # Notifikasi testimoni baru
│   └── Policies/
│       ├── AppointmentPolicy.php     # Authorization policies
│       └── PaymentPolicy.php         # Payment authorization
├── resources/
│   ├── views/
│   │   ├── welcome.blade.php          # Landing page publik
│   │   ├── layouts/
│   │   │   ├── app.blade.php          # Layout utama pelanggan
│   │   │   ├── admin.blade.php        # Layout admin dengan sidebar
│   │   │   └── guest.blade.php        # Layout halaman auth
│   │   ├── auth/                      # Halaman login, register, reset password, dll
│   │   ├── components/                # Komponen Blade global (button, input, modal)
│   │   ├── livewire/                  # Template tampilan komponen Livewire
│   │   └── profile/                   # Halaman profil pelanggan
│   ├── css/app.css                    # Custom CSS + animasi
│   └── js/app.js                      # Alpine.js + Livewire bootstrap
├── routes/
│   ├── web.php                        # Rute utama & admin
│   └── auth.php                       # Rute autentikasi
├── database/
│   └── migrations/                    # Skema tabel database
├── tailwind.config.js                 # Design system token (warna, font)
└── vite.config.js                     # Konfigurasi bundler
```

---

## �️ Arsitektur & Refactoring

### Code Quality Improvements (Phase 1)

Proyek telah melalui refactoring **Phase 1** untuk meningkatkan kualitas kode berdasarkan prinsip **Clean Code**, **DRY**, dan **KISS**:

**1. Type-Safe Enums** (Tidak ada hardcoded string lagi!)
```php
// ❌ BEFORE: Hardcoded strings
$order->update(['status' => 'dijahit']);
if ($order->status === 'dijahit') { ... }

// ✅ AFTER: Type-safe enum
$order->update(['status' => OrderStatus::DIJAHIT->value]);
if ($order->status === OrderStatus::DIJAHIT->value) { ... }
```

**2. Centralized Services** (DRY principle)
- **PaymentService** — Semua logika pembayaran (cek verified, calculate sisa, check lunas)
- **OrderBusinessRulesService** — Validasi & perhitungan bisnis order

**3. Model Encapsulation** (Logical grouping)
```php
// Order model dengan convenience methods
$order->canBeCancelled();  // Check eligibility
$order->cancel('Alasan');  // Safe cancellation
$order->isCancelled();      // Status check
```

### Service-Oriented Refactoring (Phase 2)

**Phase 2** memecah **AdminOrdersShow** yang kompleks (700+ lines) menjadi 4 service yang focused:

| Service | Tanggung Jawab | Methods |
|---------|----------------|---------|
| **OrderStatusTransitionService** | Workflow status + validasi | `acceptOrder()`, `moveToQueue()`, `updateProductionStatus()` |
| **OrderRejectionService** | Rejection logic | `rejectOrder()`, `canRejectOrder()` |
| **OrderPricingService** | DP & pricing | `setDpAmount()`, `recalculateEstimation()`, `setEstimatedPrice()` |
| **OrderMaterialService** | Material management | `setMaterialSource()`, `markMaterialReady()` |

**Benefits:**
- ✅ Single Responsibility — Setiap service fokus pada satu domain
- ✅ Reusability — Services digunakan di components, controllers, jobs
- ✅ Testability — Mudah di-unit test secara isolated
- ✅ Maintainability — Changes terisolasi di satu service

---

## 📚 Documentation

### Developer Resources

Proyek menyediakan dokumentasi lengkap untuk memudahkan onboarding & maintenance:

| File | Konten |
|------|--------|
| **CODE_REFACTORING_GUIDE.md** | Panduan prinsip refactoring + contoh sebelum/sesudah |
| **CONTRIBUTING.md** | Guidelines development, naming conventions, patterns |
| **QUICK_REFERENCE.md** | Quick lookup untuk common patterns & troubleshooting |
| **PHASE_2_SERVICES.md** | Arsitektur Phase 2 services + design patterns |
| **PHASE_2_REFACTORING_COMPLETE.md** | Summary lengkap hasil Phase 2 refactoring |
| **CODE_QUALITY_ANALYSIS.md** | Analisis peningkatan kualitas kode |
| **REFACTORING_REPORT.md** | Report metrics & verification Phase 1 |

### How to Read Documentation

1. **Untuk memahami overall architecture**: Mulai dengan README ini
2. **Untuk onboarding developer baru**: Baca [CONTRIBUTING.md](./CONTRIBUTING.md)
3. **Untuk coding guidelines**: Lihat [CODE_REFACTORING_GUIDE.md](./CODE_REFACTORING_GUIDE.md)
4. **Untuk quick lookup**: Gunakan [QUICK_REFERENCE.md](./QUICK_REFERENCE.md)
5. **Untuk Phase 2 details**: Baca [PHASE_2_SERVICES.md](./PHASE_2_SERVICES.md)

---

## �🎓 Dibuat Untuk

Proyek ini dikembangkan sebagai **tugas mata kuliah Rekayasa Perangkat Lunak (RPL)** — Semester 4.

---

## � Best Practices & Patterns

### Using Services (Phase 2)

```php
// ✅ Recommended: Inject via constructor (Laravel DI)
class AdminOrdersShow extends Component
{
    public function acceptOrder(): void
    {
        $service = app(OrderStatusTransitionService::class);
        $result = $service->acceptOrder($this->order, auth()->id());
        
        if ($result['success']) {
            session()->flash('success', $result['message']);
        }
    }
}

// ✅ Or use service container directly
app(OrderPricingService::class)->setDpAmount($order, 500000, auth()->id());
```

### Enum Usage

```php
// ✅ Type-safe status
use App\Enums\OrderStatus;

$order->status === OrderStatus::DIJAHIT->value;
OrderStatus::DIJAHIT->label(); // "Sedang Dijahit"

// ✅ Get allowed transitions
$nextStatuses = OrderStatus::SELESAI->nextStatuses(); // Available transitions
```

### Structured Responses

```php
// Services return structured array, not exceptions
$result = $service->someAction($param);

if ($result['success']) {
    // Handle success: $result['message'], $result['newStatus'], etc
} else {
    // Handle error: $result['message'], $result['errors']
    session()->flash('error', implode(', ', $result['errors'] ?? []));
}
```

---

## 🤝 Contributing

Kontribusi diterima! Pastikan mengikuti guidelines:

1. **Clone & setup** (lihat instalasi di atas)
2. **Baca** [CONTRIBUTING.md](./CONTRIBUTING.md) untuk coding standards
3. **Create feature branch**: `git checkout -b feature/your-feature`
4. **Follow** Clean Code + KISS + DRY principles
5. **Use services** untuk business logic (jangan taruh di component/controller)
6. **Add tests** untuk feature baru
7. **Commit** dengan pesan yang jelas
8. **Push & create PR** dengan deskripsi detail

### Development Workflow

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev

# Run tests
php artisan test

# Code quality checks
php artisan pint          # Format code
php artisan tinker        # Interactive shell untuk testing
```

---

## 📞 Kontak & Support

Untuk pertanyaan atau issue, silakan:
- Open issue di [GitHub](https://github.com/fanmaull03/sistem_Jahitly/issues)
- Lihat dokumentasi di folder proyek
- Baca [QUICK_REFERENCE.md](./QUICK_REFERENCE.md) untuk troubleshooting umum

---

## �📄 Lisensi

Proyek ini dibuat untuk keperluan akademik dan pengembangan internal.

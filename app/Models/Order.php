<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Order Model - Merepresentasikan satu order/pesanan penjahitan
 * 
 * Model ini menangani data order termasuk:
 * - Informasi pesanan (service, bahan, jumlah, harga estimasi)
 * - Status order (menunggu_potong, diukur, dimulai, dijahit, finishing, selesai, dll)
 * - Status material (bawa_sendiri, po, ready)
 * - Hubungan dengan customer, pembayaran, appointment, dan design files
 * 
 * Catatan: Untuk logika pembayaran yang kompleks, gunakan PaymentService
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'service_id',
        'fabric_id',
        'status',
        'material_source',
        'material_status',
        'quantity',
        'notes',
        'estimated_price',
        'estimated_finish_date',
        'order_number',
        'queue_position',
        'cancelled_at',
        'cancellation_reason',
        'rejection_reason',
        'dp_amount',
        'production_days',
        'po_days',
        'production_started_at',
        'production_finished_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_price' => 'decimal:2',
            'dp_amount' => 'decimal:2',
            'estimated_finish_date' => 'date',
            'cancelled_at' => 'datetime',
            'production_started_at' => 'datetime',
            'production_finished_at' => 'datetime',
            'quantity' => 'integer',
            'queue_position' => 'integer',
            'production_days' => 'integer',
            'po_days' => 'integer',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * Order dimiliki oleh seorang customer (user).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Order terkait dengan satu service.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Order terkait dengan satu bahan kain (opsional).
     */
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class);
    }

    /**
     * Order memiliki banyak status log.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    /**
     * Order memiliki banyak payment.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Order memiliki banyak design file.
     */
    public function designFiles(): HasMany
    {
        return $this->hasMany(DesignFile::class);
    }

    /**
     * Order memiliki satu appointment.
     */
    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }

    /**
     * Order memiliki satu testimonial.
     */
    public function testimonial(): HasOne
    {
        return $this->hasOne(Testimonial::class, 'order_id');
    }

    // ──────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────

    /**
     * Menghitung status pembayaran berdasarkan relasi payments.
     *
     * Return values:
     * - 'belum_bayar'    : belum ada pembayaran terverifikasi
     * - 'dp'             : hanya DP yang terverifikasi
     * - 'lunas'          : pelunasan sudah terverifikasi
     * - 'menunggu'       : ada pembayaran yang menunggu verifikasi
     */
    public function getPaymentStatusAttribute(): string
    {
        $payments = $this->payments;

        // Cek apakah ada pelunasan terverifikasi
        $hasVerifiedPelunasan = $payments
            ->where('payment_type', 'pelunasan')
            ->where('status', 'terverifikasi')
            ->isNotEmpty();

        if ($hasVerifiedPelunasan) {
            return 'lunas';
        }

        // Cek apakah ada DP terverifikasi
        $hasVerifiedDp = $payments
            ->where('payment_type', 'dp')
            ->where('status', 'terverifikasi')
            ->isNotEmpty();

        if ($hasVerifiedDp) {
            return 'dp';
        }

        // Cek apakah ada pembayaran yang menunggu verifikasi
        $hasPending = $payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        if ($hasPending) {
            return 'menunggu';
        }

        return 'belum_bayar';
    }

    /**
     * Cek apakah pesanan membutuhkan fitting.
     */
    public function requiresFitting(): bool
    {
        return in_array($this->service?->type, ['custom', 'seragam'], true);
    }

    /**
     * Cek apakah pesanan sedang aktif (belum selesai/ditolak/dibatalkan).
     */
    public function isActive(): bool
    {
        return !in_array($this->status, ['selesai', 'ditolak', 'dibatalkan'], true);
    }

    // ──────────────────────────────────────────────
    // Cancellation Methods (KISS Principle)
    // ──────────────────────────────────────────────

    /**
     * Cek apakah order bisa dibatalkan
     * 
     * Aturan pembatalan:
     * - Order belum selesai/ditolak/dibatalkan
     * - Tidak ada pembayaran terverifikasi
     * 
     * @return bool true jika order bisa dibatalkan
     */
    public function canBeCancelled(): bool
    {
        // Status order harus masih aktif
        if (!$this->isActive()) {
            return false;
        }

        // Tidak boleh ada pembayaran yang sudah terverifikasi
        return !$this->payments()
            ->where('status', 'terverifikasi')
            ->exists();
    }

    /**
     * Membatalkan order dengan alasan
     * 
     * Method ini mengenkapsulasi logika pembatalan order.
     * Sebelum memanggil method ini, pastikan sudah validasi dengan canBeCancelled()
     * 
     * @param string $reason Alasan pembatalan (minimal 10 karakter, maksimal 500)
     * @return bool true jika berhasil dibatalkan
     */
    public function cancel(string $reason): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => 'dibatalkan',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        // Log status transition
        OrderStatusLog::create([
            'order_id' => $this->id,
            'from_status' => 'dibatalkan',
            'to_status' => 'dibatalkan',
            'notes' => 'Order dibatalkan oleh customer: ' . $reason,
        ]);

        return true;
    }

    /**
     * Cek apakah order sudah dibatalkan
     * 
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'dibatalkan' && $this->cancelled_at !== null;
    }

    /**
     * Mendapatkan alasan pembatalan (jika ada)
     * 
     * @return string|null Alasan pembatalan atau null jika tidak ada
     */
    public function getCancellationReason(): ?string
    {
        return $this->cancellation_reason;
    }
}

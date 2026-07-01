<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model - Merepresentasikan satu transaksi pembayaran
 * 
 * Model ini menangani:
 * - Informasi pembayaran (jumlah, metode, tipe: dp/pelunasan)
 * - Status pembayaran (belum_bayar, menunggu_verifikasi, terverifikasi, ditolak)
 * - Bukti pembayaran (file path)
 * - Verifikasi oleh admin (verified_by, verified_at, rejection_note)
 * 
 * Catatan: Untuk logika pembayaran kompleks, lihat PaymentService
 */
class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'customer_id',
        'payment_type',
        'payment_method',
        'amount',
        'proof_file_path',
        'status',
        'rejection_note',
        'verified_by',
        'verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * Payment ini milik satu order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Payment ini milik seorang customer (user).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Payment ini diverifikasi oleh seorang admin (user).
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

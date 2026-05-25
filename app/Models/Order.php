<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'status',
        'material_source',
        'material_status',
        'quantity',
        'notes',
        'estimated_price',
        'estimated_finish_date',
        'order_number',
        'queue_position',
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
            'estimated_finish_date' => 'date',
            'quantity' => 'integer',
            'queue_position' => 'integer',
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
}

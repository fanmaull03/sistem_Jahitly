<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fabric extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'color',
        'price_per_meter',
        'stock_status',
        'po_days',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_meter' => 'decimal:2',
            'po_days' => 'integer',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * Fabric digunakan oleh banyak order.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Cek apakah bahan tersedia (bukan PO).
     */
    public function isAvailable(): bool
    {
        return $this->stock_status === 'tersedia';
    }

    /**
     * Label kategori yang lebih ramah dibaca.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'katun' => 'Katun',
            'polyester' => 'Polyester',
            'linen' => 'Linen',
            'sutra' => 'Sutra',
            'denim' => 'Denim',
            'sifon' => 'Sifon',
            'wol' => 'Wol',
            'lainnya' => 'Lainnya',
            default => ucfirst($this->category),
        };
    }
}

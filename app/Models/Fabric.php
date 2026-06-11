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
        'stock_meters',
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
            'stock_meters' => 'decimal:2',
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
     * Kurangi stok bahan dalam satuan meter.
     * Jika stok habis, otomatis ubah status ke PO.
     */
    public function deductStock(float $meters): void
    {
        $this->stock_meters = max(0, (float) $this->stock_meters - $meters);

        // Otomatis ubah status ke PO jika stok habis
        if ($this->stock_meters <= 0) {
            $this->stock_status = 'po';
        }

        $this->save();
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

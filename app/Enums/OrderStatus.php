<?php

namespace App\Enums;

/**
 * OrderStatus Enum - Mendefinisikan semua status yang mungkin untuk sebuah order
 * 
 * Status Flow:
 * menunggu_potong -> diukur -> dimulai -> dijahit -> finishing -> selesai
 * 
 * Catatan: Order dapat dibatalkan atau ditolak dari status manapun (kecuali selesai)
 */
enum OrderStatus: string
{
    /**
     * Order baru, menunggu untuk dipotong/diproses
     */
    case MENUNGGU_POTONG = 'menunggu_potong';

    /**
     * Sedang dalam proses pengukuran
     */
    case DIUKUR = 'diukur';

    /**
     * Produksi telah dimulai
     */
    case DIMULAI = 'dimulai';

    /**
     * Sedang dalam proses penjahitan
     */
    case DIJAHIT = 'dijahit';

    /**
     * Dalam tahap finishing (pressing, quality check, dll)
     */
    case FINISHING = 'finishing';

    /**
     * Order selesai dan siap untuk diambil
     */
    case SELESAI = 'selesai';

    /**
     * Order dibatalkan oleh customer
     */
    case DIBATALKAN = 'dibatalkan';

    /**
     * Order ditolak oleh admin/sistem
     */
    case DITOLAK = 'ditolak';

    /**
     * Mendapatkan deskripsi human-readable untuk status
     */
    public function label(): string
    {
        return match($this) {
            self::MENUNGGU_POTONG => 'Menunggu Potong',
            self::DIUKUR => 'Diukur',
            self::DIMULAI => 'Dimulai',
            self::DIJAHIT => 'Dijahit',
            self::FINISHING => 'Finishing',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
            self::DITOLAK => 'Ditolak',
        };
    }

    /**
     * Cek apakah status adalah terminal (tidak bisa dipindahkan)
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::SELESAI,
            self::DIBATALKAN,
            self::DITOLAK,
        ], true);
    }

    /**
     * Cek apakah order aktif (belum selesai/dibatalkan/ditolak)
     */
    public function isActive(): bool
    {
        return !$this->isTerminal();
    }

    /**
     * Mendapatkan status yang dapat ditransisikan dari status sekarang
     * 
     * @return array<OrderStatus>
     */
    public function nextStatuses(): array
    {
        return match($this) {
            self::MENUNGGU_POTONG => [self::DIUKUR, self::DITOLAK, self::DIBATALKAN],
            self::DIUKUR => [self::DIMULAI, self::DITOLAK, self::DIBATALKAN],
            self::DIMULAI => [self::DIJAHIT, self::DITOLAK, self::DIBATALKAN],
            self::DIJAHIT => [self::FINISHING, self::DITOLAK, self::DIBATALKAN],
            self::FINISHING => [self::SELESAI, self::DITOLAK, self::DIBATALKAN],
            self::SELESAI, self::DIBATALKAN, self::DITOLAK => [],
        };
    }

    /**
     * Cek apakah bisa transition ke status tertentu
     */
    public function canTransitionTo(self $targetStatus): bool
    {
        return in_array($targetStatus, $this->nextStatuses(), true);
    }
}

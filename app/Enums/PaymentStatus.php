<?php

namespace App\Enums;

/**
 * PaymentStatus Enum - Mendefinisikan semua status yang mungkin untuk sebuah pembayaran
 * 
 * Status Flow: belum_bayar -> menunggu_verifikasi -> terverifikasi/ditolak
 */
enum PaymentStatus: string
{
    /**
     * Pembayaran belum dibuat/belum dilakukan
     */
    case BELUM_BAYAR = 'belum_bayar';

    /**
     * Bukti pembayaran sudah diunggah, menunggu verifikasi admin
     */
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    /**
     * Pembayaran sudah diverifikasi dan diterima
     */
    case TERVERIFIKASI = 'terverifikasi';

    /**
     * Pembayaran ditolak oleh admin (bukti tidak valid, jumlah kurang, dll)
     */
    case DITOLAK = 'ditolak';

    /**
     * Mendapatkan deskripsi human-readable untuk status pembayaran
     */
    public function label(): string
    {
        return match($this) {
            self::BELUM_BAYAR => 'Belum Bayar',
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::DITOLAK => 'Ditolak',
        };
    }

    /**
     * Cek apakah status adalah terminal (tidak bisa berubah)
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::TERVERIFIKASI,
            self::DITOLAK,
        ], true);
    }

    /**
     * Cek apakah pembayaran sudah diterima/valid
     */
    public function isApproved(): bool
    {
        return $this === self::TERVERIFIKASI;
    }

    /**
     * Cek apakah pembayaran menunggu tindakan
     */
    public function isPending(): bool
    {
        return $this === self::MENUNGGU_VERIFIKASI;
    }

    /**
     * Mendapatkan warna badge untuk status (untuk UI)
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::BELUM_BAYAR => 'gray',
            self::MENUNGGU_VERIFIKASI => 'yellow',
            self::TERVERIFIKASI => 'green',
            self::DITOLAK => 'red',
        };
    }
}

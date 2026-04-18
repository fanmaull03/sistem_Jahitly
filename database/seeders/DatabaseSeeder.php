<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ──────────────────────────────────────────
        // Users
        // ──────────────────────────────────────────

        User::updateOrCreate(
            ['email' => 'admin@jahitly.com'],
            [
                'name' => 'Admin Jahitly',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@jahitly.com'],
            [
                'name' => 'Customer Test',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // ──────────────────────────────────────────
        // Services
        // ──────────────────────────────────────────

        Service::updateOrCreate(
            ['name' => 'Vermak Pakaian'],
            [
                'description' => 'Layanan vermak dan perbaikan pakaian. Termasuk pemendekan celana, penyempitan baju, penggantian resleting, dan perbaikan jahitan.',
                'type' => 'vermak',
                'base_price' => 25000.00,
                'base_duration_days' => 3,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Jahit Seragam'],
            [
                'description' => 'Layanan pembuatan seragam sekolah, kantor, dan organisasi. Tersedia dalam berbagai ukuran dengan bahan berkualitas.',
                'type' => 'seragam',
                'base_price' => 150000.00,
                'base_duration_days' => 7,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Jahit Custom'],
            [
                'description' => 'Layanan jahit pakaian custom sesuai desain Anda. Mulai dari kemeja, dress, blazer, gamis, hingga jas. Konsultasi desain gratis.',
                'type' => 'custom',
                'base_price' => 250000.00,
                'base_duration_days' => 14,
            ]
        );
    }
}

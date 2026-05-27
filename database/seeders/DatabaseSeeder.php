<?php

namespace Database\Seeders;

use App\Models\Fabric;
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

        // ──────────────────────────────────────────
        // Fabrics (Bahan Kain)
        // ──────────────────────────────────────────

        $fabrics = [
            [
                'name' => 'Katun Combed 30s',
                'category' => 'katun',
                'color' => 'Putih',
                'price_per_meter' => 45000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Kain katun combed halus, cocok untuk kaos dan kemeja kasual.',
            ],
            [
                'name' => 'Katun Combed 30s',
                'category' => 'katun',
                'color' => 'Hitam',
                'price_per_meter' => 45000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Kain katun combed halus warna hitam, cocok untuk kaos dan kemeja kasual.',
            ],
            [
                'name' => 'Katun Oxford',
                'category' => 'katun',
                'color' => 'Biru Muda',
                'price_per_meter' => 65000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Kain oxford bertekstur, ideal untuk kemeja formal dan semi-formal.',
            ],
            [
                'name' => 'Drill',
                'category' => 'katun',
                'color' => 'Khaki',
                'price_per_meter' => 55000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Kain drill tebal dan kuat, cocok untuk seragam kerja dan celana.',
            ],
            [
                'name' => 'Drill',
                'category' => 'katun',
                'color' => 'Navy',
                'price_per_meter' => 55000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Kain drill tebal warna navy, cocok untuk seragam dan celana formal.',
            ],
            [
                'name' => 'Polyester Premium',
                'category' => 'polyester',
                'color' => 'Putih',
                'price_per_meter' => 35000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Polyester premium anti-kusut, cocok untuk seragam dan jas laboratorium.',
            ],
            [
                'name' => 'Linen Premium',
                'category' => 'linen',
                'color' => 'Cream',
                'price_per_meter' => 120000.00,
                'stock_status' => 'po',
                'po_days' => 7,
                'description' => 'Linen premium import, adem dan elegan untuk pakaian kasual mewah.',
            ],
            [
                'name' => 'Sutra Mutiara',
                'category' => 'sutra',
                'color' => 'Gold',
                'price_per_meter' => 250000.00,
                'stock_status' => 'po',
                'po_days' => 14,
                'description' => 'Sutra mutiara berkualitas tinggi, cocok untuk gaun pesta dan kebaya.',
            ],
            [
                'name' => 'Denim Stretch',
                'category' => 'denim',
                'color' => 'Biru Tua',
                'price_per_meter' => 75000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Denim stretch nyaman, cocok untuk celana jeans dan jaket casual.',
            ],
            [
                'name' => 'Sifon Ceruti',
                'category' => 'sifon',
                'color' => 'Dusty Pink',
                'price_per_meter' => 85000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Sifon ceruti jatuh dan lembut, ideal untuk gamis dan hijab.',
            ],
            [
                'name' => 'Wol Blend',
                'category' => 'wol',
                'color' => 'Abu-abu',
                'price_per_meter' => 180000.00,
                'stock_status' => 'po',
                'po_days' => 10,
                'description' => 'Campuran wol premium, cocok untuk jas dan blazer formal.',
            ],
            [
                'name' => 'Twill Cotton',
                'category' => 'katun',
                'color' => 'Olive',
                'price_per_meter' => 60000.00,
                'stock_status' => 'tersedia',
                'po_days' => null,
                'description' => 'Kain twill katun dengan tekstur diagonal, cocok untuk celana chino dan jaket.',
            ],
        ];

        foreach ($fabrics as $fabric) {
            Fabric::updateOrCreate(
                ['name' => $fabric['name'], 'color' => $fabric['color']],
                $fabric
            );
        }
    }
}

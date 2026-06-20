<?php

use App\Models\AlterationOption;

$options = [
    ['name' => 'Pasang Kancing / Hak (Per Pcs)', 'price' => 10000, 'description' => 'Memasang kancing atau hak yang terlepas.', 'is_active' => true],
    ['name' => 'Tambal Robek / Bolong Ringan', 'price' => 10000, 'description' => 'Menambal bagian pakaian yang robek kecil atau bolong.', 'is_active' => true],
    ['name' => 'Potong Panjang Celana / Rok Biasa', 'price' => 15000, 'description' => 'Memotong dan merapikan ujung bawah celana kain atau rok.', 'is_active' => true],
    ['name' => 'Potong Lengan Baju / Kemeja', 'price' => 15000, 'description' => 'Memotong lengan baju menjadi lebih pendek.', 'is_active' => true],
    ['name' => 'Ganti Resleting Celana / Rok', 'price' => 15000, 'description' => 'Mengganti resleting celana atau rok yang rusak.', 'is_active' => true],
    ['name' => 'Kecilkan Pinggang Celana / Rok', 'price' => 20000, 'description' => 'Mengecilkan lingkar pinggang agar pas di badan.', 'is_active' => true],
    ['name' => 'Potong Celana Jeans (Original Hem)', 'price' => 25000, 'description' => 'Memotong jeans dengan mempertahankan jahitan ujung asli.', 'is_active' => true],
    ['name' => 'Kecilkan Body Baju / Kemeja', 'price' => 30000, 'description' => 'Mengecilkan bagian samping kemeja atau baju.', 'is_active' => true],
    ['name' => 'Ganti Resleting Jaket', 'price' => 35000, 'description' => 'Mengganti resleting utama pada jaket panjang.', 'is_active' => true],
    ['name' => 'Kecilkan Body Jas / Blazer', 'price' => 45000, 'description' => 'Mengecilkan bagian body pada jas atau blazer (butuh keahlian khusus).', 'is_active' => true],
];

foreach ($options as $opt) {
    AlterationOption::updateOrCreate(
        ['name' => $opt['name']],
        $opt
    );
}

echo "Berhasil menambahkan " . count($options) . " opsi vermak!\n";

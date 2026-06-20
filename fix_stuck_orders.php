<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = App\Models\Order::where('status', 'menunggu_appointment')->get();
foreach($orders as $o) {
    if($o->appointment && $o->appointment->status === 'selesai') {
        $o->update(['status' => 'menunggu_bahan']);
        echo "Updated order: " . $o->order_number . "\n";
    }
}

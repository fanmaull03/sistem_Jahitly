<?php

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\DesignFile;

$orders = Order::whereHas('service', function($q) {
    $q->where('type', 'vermak');
})->get();

$count = 0;
foreach ($orders as $order) {
    OrderStatusLog::where('order_id', $order->id)->delete();
    Payment::where('order_id', $order->id)->delete();
    DesignFile::where('order_id', $order->id)->delete();
    $order->delete();
    $count++;
}

echo "Berhasil menghapus {$count} pesanan vermak.\n";

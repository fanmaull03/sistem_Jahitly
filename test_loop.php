<?php
try {
    $order = App\Models\Order::first();
    $component = Livewire\Livewire::test('admin.orders.show', ['order' => $order]);
    echo 'SUCCESS';
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}

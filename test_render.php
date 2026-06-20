<?php
try {
    $admin = App\Models\User::where('role', 'admin')->first();
    auth()->login($admin);
    
    $order = App\Models\Order::first();
    $html = \Illuminate\Support\Facades\Blade::render(
        "<div>@livewire('admin.orders.show', ['order' => App\Models\Order::first()])</div>"
    );
    echo "SUCCESS: " . substr($html, 0, 50) . "...";
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}

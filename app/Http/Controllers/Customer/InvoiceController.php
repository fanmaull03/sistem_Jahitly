<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show(Order $order)
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['customer', 'service', 'fabric', 'payments']);

        $serviceTotal = $order->service->base_price * $order->quantity;
        $fabricTotal = 0;
        
        if ($order->fabric && $order->service->type !== 'vermak') {
            $fabricTotal = $order->fabric->price_per_meter * $order->quantity;
        }

        $vermakTotal = 0;
        if ($order->service->type === 'vermak' && $order->alteration_details) {
            $details = json_decode($order->alteration_details, true) ?? [];
            foreach ($details as $detail) {
                $vermakTotal += (float) ($detail['price'] ?? 0);
            }
            $vermakTotal *= $order->quantity;
        }

        $subtotal = $serviceTotal + $fabricTotal + $vermakTotal;
        $adjustment = $order->estimated_price - $subtotal;
        $grandTotal = $order->estimated_price;

        $totalPaid = $order->payments->where('status', 'terverifikasi')->sum('amount');
        // Anggap lunas jika pesanan statusnya selesai atau pembayaran sudah mencapai harga estimasi
        $isLunas = $order->status === 'selesai' || $totalPaid >= $order->estimated_price;

        return view('customer.orders.invoice', compact('order', 'serviceTotal', 'fabricTotal', 'subtotal', 'adjustment', 'grandTotal', 'isLunas'));
    }

    public function showPayment(\App\Models\Payment $payment)
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $order = $payment->order;

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($payment->status !== 'terverifikasi') {
            abort(403, 'Invoice hanya tersedia untuk pembayaran yang sudah terverifikasi.');
        }

        $order->load(['customer', 'service', 'fabric', 'payments']);

        $serviceTotal = $order->service->base_price * $order->quantity;
        $fabricTotal = 0;
        
        if ($order->fabric && $order->service->type !== 'vermak') {
            $fabricTotal = $order->fabric->price_per_meter * $order->quantity;
        }

        $vermakTotal = 0;
        if ($order->service->type === 'vermak' && $order->alteration_details) {
            $details = json_decode($order->alteration_details, true) ?? [];
            foreach ($details as $detail) {
                $vermakTotal += (float) ($detail['price'] ?? 0);
            }
            $vermakTotal *= $order->quantity;
        }

        $subtotal = $serviceTotal + $fabricTotal + $vermakTotal;
        $adjustment = $order->estimated_price - $subtotal;
        $grandTotal = $order->estimated_price;

        $isLunas = true; // Karena ini invoice pembayaran yg sudah terverifikasi, maka statusnya LUNAS untuk tagihan ini

        return view('customer.orders.invoice', compact('order', 'payment', 'serviceTotal', 'fabricTotal', 'subtotal', 'adjustment', 'grandTotal', 'isLunas'));
    }
}

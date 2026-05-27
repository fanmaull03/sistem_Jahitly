<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Testimonial;
use App\Models\User;
use App\Notifications\NewTestimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Redirect;

class TestimonialController extends Controller
{
    public function store(HttpRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $order = Order::findOrFail($data['order_id']);

        // Pastikan order milik user
        if ($order->customer_id !== $request->user()->id) {
            abort(403, 'Anda tidak berhak memberikan testimoni untuk pesanan ini.');
        }

        // Cek apakah sudah ada testimonial untuk order ini
        if ($order->testimonial) {
            return Redirect::back()->with('error', 'Testimoni untuk pesanan ini sudah ada.');
        }

        $testimonial = Testimonial::create([
            'customer_id' => $request->user()->id,
            'order_id' => $order->id,
            'rating' => $data['rating'] ?? null,
            'comment' => $data['comment'],
        ]);

        // Notify all admins about the new testimonial
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewTestimonial($testimonial));
        }

        return Redirect::back()->with('success', 'Terima kasih atas testimoni Anda.');
    }
}

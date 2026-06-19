<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getSummaryProperty(): array
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        return [
            'active_orders' => Order::where('status', '!=', 'selesai')->count(),
            'pending_payments' => Payment::where('status', 'menunggu_verifikasi')->count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $now->toDateString())->count(),
            'monthly_revenue' => Payment::where('status', 'terverifikasi')
                ->whereBetween('verified_at', [$monthStart, $monthEnd])
                ->sum('amount'),
        ];
    }

    public function render(): View
    {
        $todayAppointments = Appointment::with(['customer', 'order.service'])
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('livewire.admin.dashboard', [
            'summary' => $this->summary,
            'todayAppointments' => $todayAppointments,
        ])->layout('layouts.admin');
    }
}

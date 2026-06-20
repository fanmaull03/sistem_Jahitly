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
            'pending_orders' => Order::where('status', 'menunggu_konfirmasi')->count(),
            'active_orders' => Order::whereNotIn('status', ['selesai', 'ditolak', 'dibatalkan', 'menunggu_konfirmasi'])->count(),
            'pending_payments' => Payment::where('status', 'menunggu_verifikasi')->count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $now->toDateString())->count(),
            'monthly_revenue' => Payment::where('status', 'terverifikasi')
                ->whereBetween('verified_at', [$monthStart, $monthEnd])
                ->sum('amount'),
            'completed_orders' => Order::where('status', 'selesai')->count(),
        ];
    }

    public string $chartFilter = '6_months';

    public function updatedChartFilter()
    {
        $this->dispatch('update-revenue-chart', chartData: $this->getRevenueChartData());
    }

    private function getRevenueChartData(): array
    {
        $labels = collect();
        $revenues = collect();

        switch ($this->chartFilter) {
            case '1_week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $labels->push($date->translatedFormat('D, d M'));
                    
                    $sum = Payment::where('status', 'terverifikasi')
                        ->whereDate('verified_at', $date)
                        ->sum('amount');
                    $revenues->push($sum);
                }
                break;
                
            case '1_month':
                for ($i = 3; $i >= 0; $i--) {
                    $start = Carbon::today()->subWeeks($i)->startOfWeek();
                    $end = Carbon::today()->subWeeks($i)->endOfWeek();
                    $labels->push($start->format('d M') . ' - ' . $end->format('d M'));
                    
                    $sum = Payment::where('status', 'terverifikasi')
                        ->whereBetween('verified_at', [$start->startOfDay(), $end->endOfDay()])
                        ->sum('amount');
                    $revenues->push($sum);
                }
                break;
                
            case '1_year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels->push($date->translatedFormat('M Y'));
                    
                    $start = $date->copy()->startOfMonth();
                    $end = $date->copy()->endOfMonth();
                    
                    $sum = Payment::where('status', 'terverifikasi')
                        ->whereBetween('verified_at', [$start, $end])
                        ->sum('amount');
                    $revenues->push($sum);
                }
                break;
                
            case '6_months':
            default:
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels->push($date->translatedFormat('F'));
                    
                    $start = $date->copy()->startOfMonth();
                    $end = $date->copy()->endOfMonth();
                    
                    $sum = Payment::where('status', 'terverifikasi')
                        ->whereBetween('verified_at', [$start, $end])
                        ->sum('amount');
                    $revenues->push($sum);
                }
                break;
        }

        return [
            'labels' => $labels->toArray(),
            'data' => $revenues->toArray(),
        ];
    }

    public function render(): View
    {
        $todayAppointments = Appointment::with(['customer', 'order.service'])
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->get();

        $recentOrders = Order::with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'summary' => $this->summary,
            'todayAppointments' => $todayAppointments,
            'recentOrders' => $recentOrders,
            'revenueChartData' => $this->getRevenueChartData(),
        ])->layout('layouts.admin');
    }
}

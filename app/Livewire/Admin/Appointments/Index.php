<?php

namespace App\Livewire\Admin\Appointments;

use App\Models\Appointment;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;
use App\Services\OrderBusinessRulesService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?string $dateFilter = null;

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function getAppointmentsProperty()
    {
        $query = Appointment::with(['order.service', 'customer'])
            ->orderBy('appointment_date');

        if ($this->dateFilter) {
            $query->whereDate('appointment_date', $this->dateFilter);
        }

        return $query->paginate(15);
    }

    public function confirm(int $appointmentId): void
    {
        $appointment = Appointment::with('order')->findOrFail($appointmentId);

        if ($appointment->status !== 'menunggu') {
            session()->flash('error', 'Hanya appointment berstatus menunggu yang dapat dikonfirmasi.');
            return;
        }

        $appointment->update(['status' => 'terkonfirmasi']);

        session()->flash('success', 'Appointment untuk pesanan '
            . $appointment->order->order_number . ' berhasil dikonfirmasi.');
    }

    public function complete(int $appointmentId): void
    {
        $appointment = Appointment::with(['order', 'order.customer', 'order.payments', 'order.service'])
            ->findOrFail($appointmentId);

        if ($appointment->status !== 'terkonfirmasi') {
            session()->flash('error', 'Hanya appointment berstatus terkonfirmasi yang dapat diselesaikan.');
            return;
        }

        $appointment->update(['status' => 'selesai']);

        $order = $appointment->order;
        $message = 'Appointment berhasil ditandai selesai.';

        $check = app(OrderBusinessRulesService::class)->canMoveToProcessing($order);

        if ($check['can_proceed']) {
            $order->update(['status' => 'diproses']);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'diproses',
                'changed_by' => auth()->id(),
                'notes' => 'Status otomatis berubah setelah appointment selesai dan semua syarat terpenuhi.',
            ]);

            if ($order->customer) {
                $message = 'Pesanan #' . $order->order_number . ' status diperbarui menjadi diproses.';
                $order->customer->notify(new OrderStatusUpdated($order, $message));
            }

            $message .= ' Pesanan ' . $order->order_number . ' otomatis diproses.';
        } else {
            $message .= ' Pesanan ' . $order->order_number
                . ' belum bisa diproses: ' . implode(' ', $check['blocking_reasons']);
        }

        session()->flash('success', $message);
    }

    public function render(): View
    {
        return view('livewire.admin.appointments.index', [
            'appointments' => $this->appointments,
        ])->layout('layouts.admin');
    }
}

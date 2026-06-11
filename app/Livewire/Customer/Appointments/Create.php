<?php

namespace App\Livewire\Customer\Appointments;

use App\Models\Appointment;
use App\Models\Order;
use App\Services\OrderBusinessRulesService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public Order $order;
    public ?string $selectedDate = null;
    public ?int $selectedHour = null;
    public ?string $notes = null;
    public array $availableSlots = [];
    public bool $hasExistingAppointment = false;

    public function mount(Order $order): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['service', 'appointment']);

        // Cek apakah layanan membutuhkan appointment
        if (! in_array($order->service->type, ['seragam', 'custom'], true)) {
            $this->redirect(route('orders.show', $order), navigate: true);
            return;
        }

        $this->order = $order;
        $this->hasExistingAppointment = $order->appointment !== null
            && $order->appointment->status !== 'dibatalkan';

        // Default selected date = besok
        $this->selectedDate = Carbon::tomorrow()->format('Y-m-d');
        $this->loadSlots();
    }

    public function updatedSelectedDate(): void
    {
        $this->selectedHour = null;
        $this->loadSlots();
    }

    public function selectSlot(int $hour): void
    {
        // Verify slot is actually available
        $slot = collect($this->availableSlots)->firstWhere('hour', $hour);
        if ($slot && $slot['available']) {
            $this->selectedHour = $hour;
        }
    }

    public function submit()
    {
        $this->validate([
            'selectedDate' => ['required', 'date', 'after:today'],
            'selectedHour' => ['required', 'integer', 'min:8', 'max:18'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'selectedDate.required' => 'Tanggal appointment harus dipilih.',
            'selectedDate.after' => 'Tanggal appointment harus di masa depan.',
            'selectedHour.required' => 'Jam appointment harus dipilih.',
        ]);

        $appointmentDate = Carbon::parse($this->selectedDate)->setTime($this->selectedHour, 0, 0);

        $orderRules = app(OrderBusinessRulesService::class);

        // Double-check availability
        if (! $orderRules->isAppointmentSlotAvailable($appointmentDate)) {
            $this->addError('selectedHour', 'Slot waktu ini sudah terisi. Silakan pilih waktu lain.');
            $this->loadSlots(); // Refresh slots
            return;
        }

        Appointment::create([
            'order_id' => $this->order->id,
            'customer_id' => auth()->id(),
            'appointment_date' => $appointmentDate,
            'status' => 'menunggu',
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Appointment berhasil dibuat untuk '
            . $appointmentDate->translatedFormat('l, d F Y H:i')
            . '. Menunggu konfirmasi admin.');

        return $this->redirectRoute('orders.show', $this->order, navigate: true);
    }

    private function loadSlots(): void
    {
        if (! $this->selectedDate) {
            $this->availableSlots = [];
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        $orderRules = app(OrderBusinessRulesService::class);
        $this->availableSlots = $orderRules->getAvailableSlots($date);
    }

    public function render(): View
    {
        return view('livewire.customer.appointments.create', [
            'minDate' => Carbon::tomorrow()->format('Y-m-d'),
            'maxDate' => Carbon::now()->addMonths(2)->format('Y-m-d'),
        ])->layout('layouts.app');
    }
}

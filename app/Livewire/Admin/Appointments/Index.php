<?php

namespace App\Livewire\Admin\Appointments;

use App\Models\Appointment;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;
use App\Services\OrderBusinessRulesService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?string $dateFilter = null;
    
    // Calendar properties
    public int $currentMonth;
    public int $currentYear;

    // Reschedule properties
    public bool $showRescheduleModal = false;
    public ?int $rescheduleAppointmentId = null;
    public ?string $rescheduleDate = null;
    public ?string $rescheduleTime = null;

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if (! $this->dateFilter) {
            $this->dateFilter = Carbon::today()->format('Y-m-d');
        }

        $this->currentMonth = Carbon::parse($this->dateFilter)->month;
        $this->currentYear = Carbon::parse($this->dateFilter)->year;
    }

    public function updatingDateFilter($value): void
    {
        if ($value) {
            $parsed = Carbon::parse($value);
            $this->currentMonth = $parsed->month;
            $this->currentYear = $parsed->year;
        }
        $this->resetPage();
    }

    public function selectDate($date): void
    {
        $this->dateFilter = $date;
        $this->resetPage();
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function getCalendarDaysProperty(): array
    {
        $startOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $startCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $appointmentDates = Appointment::selectRaw('DATE(appointment_date) as date')
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        $days = [];
        $currentDate = $startCalendar->copy();

        while ($currentDate->lte($endCalendar)) {
            $dateString = $currentDate->format('Y-m-d');
            $days[] = [
                'date' => $dateString,
                'day' => $currentDate->day,
                'is_current_month' => $currentDate->month === $this->currentMonth,
                'is_today' => $dateString === Carbon::today()->format('Y-m-d'),
                'has_appointments' => in_array($dateString, $appointmentDates),
            ];
            $currentDate->addDay();
        }

        return $days;
    }

    public function getSummaryProperty(): array
    {
        $query = Appointment::query();
        
        if ($this->dateFilter) {
            $query->whereDate('appointment_date', $this->dateFilter);
        }

        $appointments = $query->get();

        return [
            'total' => $appointments->count(),
            'menunggu' => $appointments->where('status', 'menunggu')->count(),
            'selesai' => $appointments->where('status', 'selesai')->count(),
        ];
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

    public function openRescheduleModal(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->rescheduleAppointmentId = $appointment->id;
        $this->rescheduleDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        $this->rescheduleTime = Carbon::parse($appointment->appointment_date)->format('H:i');
        $this->showRescheduleModal = true;
    }

    public function closeRescheduleModal(): void
    {
        $this->showRescheduleModal = false;
        $this->rescheduleAppointmentId = null;
        $this->rescheduleDate = null;
        $this->rescheduleTime = null;
    }

    public function saveReschedule(): void
    {
        $this->validate([
            'rescheduleDate' => 'required|date',
            'rescheduleTime' => 'required|date_format:H:i',
        ]);

        $newDatetime = Carbon::parse($this->rescheduleDate . ' ' . $this->rescheduleTime);
        $hour = (int) $newDatetime->format('H');

        // Validasi jam operasional (08:00 - 19:00)
        if ($hour < 8 || $hour >= 19) {
            $this->addError('rescheduleTime', 'Jam appointment harus antara 08:00 - 19:00.');
            return;
        }

        // Validasi jam istirahat (12:00 - 13:00)
        if ($hour >= 12 && $hour < 13) {
            $this->addError('rescheduleTime', 'Jam 12:00 - 13:00 adalah jam istirahat.');
            return;
        }

        $appointment = Appointment::findOrFail($this->rescheduleAppointmentId);

        // Cek ketersediaan slot (exclude current appointment)
        $conflicting = Appointment::where('status', '!=', 'dibatalkan')
            ->where('id', '!=', $appointment->id)
            ->where('appointment_date', '<', $newDatetime->copy()->addHour())
            ->whereRaw('DATE_ADD(appointment_date, INTERVAL 1 HOUR) > ?', [$newDatetime])
            ->exists();

        if ($conflicting) {
            $this->addError('rescheduleTime', 'Slot waktu ini sudah terisi. Pilih waktu lain.');
            return;
        }

        $appointment->update([
            'appointment_date' => $newDatetime,
            'status' => 'menunggu',
        ]);

        $this->closeRescheduleModal();
        session()->flash('success', 'Jadwal berhasil diubah ke ' . $newDatetime->translatedFormat('l, d F Y H:i') . '.');
    }

    public function render(): View
    {
        return view('livewire.admin.appointments.index', [
            'appointments' => $this->appointments,
            'calendarDays' => $this->calendarDays,
            'summary' => $this->summary,
            'monthName' => Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->translatedFormat('F Y'),
        ])->layout('layouts.admin');
    }
}

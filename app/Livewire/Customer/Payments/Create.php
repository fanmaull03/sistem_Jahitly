<?php

namespace App\Livewire\Customer\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Order $order;
    public string $payment_method = 'transfer';
    public ?float $amount = null;
    public $proof_file;
    public string $paymentType = 'dp';
    public float $totalPaid = 0.0;
    public float $remainingAmount = 0.0;
    public bool $hasPendingPayment = false;

    public function mount(Order $order): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order->load(['service', 'payments']);

        $hasVerifiedDp = $this->order->payments
            ->where('payment_type', 'dp')
            ->where('status', 'terverifikasi')
            ->isNotEmpty();

        $this->paymentType = $hasVerifiedDp ? 'pelunasan' : 'dp';
        $this->totalPaid = (float) $this->order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');
        $this->remainingAmount = max(0, (float) $this->order->estimated_price - $this->totalPaid);
        $this->hasPendingPayment = $this->order->payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        if ($this->paymentType === 'pelunasan') {
            $this->amount = $this->remainingAmount;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $proofRule = $this->requiresProofFile()
            ? ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']
            : ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];

        return [
            'payment_method' => ['required', 'in:transfer,qris,cash'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'proof_file' => $proofRule,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran harus transfer, qris, atau cash.',
            'amount.required' => 'Nominal pembayaran harus diisi.',
            'amount.numeric' => 'Nominal pembayaran harus berupa angka.',
            'amount.min' => 'Nominal pembayaran minimal Rp 1.000.',
            'proof_file.required' => 'Bukti pembayaran wajib diunggah untuk metode transfer/QRIS.',
            'proof_file.file' => 'Bukti pembayaran harus berupa file yang valid.',
            'proof_file.mimes' => 'Bukti pembayaran harus berformat JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti pembayaran maksimal 5MB.',
        ];
    }

    public function updatedPaymentMethod(): void
    {
        if (! $this->requiresProofFile()) {
            $this->proof_file = null;
            $this->resetValidation('proof_file');
        }
    }

    public function updatedProofFile(): void
    {
        $this->validateOnly('proof_file');
    }

    public function submit()
    {
        $validated = $this->validate();

        $proofFilePath = null;
        if ($this->proof_file) {
            $proofFilePath = $this->proof_file->store('payment-proofs', 'local');
        }

        Payment::create([
            'order_id' => $this->order->id,
            'customer_id' => auth()->id(),
            'payment_type' => $this->paymentType,
            'payment_method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'proof_file_path' => $proofFilePath,
            'status' => 'menunggu_verifikasi',
        ]);

        $typeLabel = $this->paymentType === 'dp' ? 'DP' : 'Pelunasan';

        session()->flash('success', 'Pembayaran ' . $typeLabel . ' sebesar Rp '
            . number_format($validated['amount'], 0, ',', '.')
            . ' berhasil dikirim dan menunggu verifikasi.');

        return $this->redirectRoute('orders.show', $this->order, navigate: true);
    }

    private function requiresProofFile(): bool
    {
        return in_array($this->payment_method, ['transfer', 'qris'], true);
    }

    public function render(): View
    {
        return view('livewire.customer.payments.create', [
            'requiresProofFile' => $this->requiresProofFile(),
        ])->layout('layouts.app');
    }
}

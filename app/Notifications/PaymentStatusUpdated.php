<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdated extends Notification
{
    use Queueable;

    public Payment $payment;
    public string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $status)
    {
        $this->payment = $payment;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isRejected = $this->status === 'ditolak';
        $orderId = $this->payment->order_id;
        $message = $isRejected
            ? 'Pembayaran Anda ditolak. Silakan unggah ulang.'
            : 'Pembayaran Anda telah diverifikasi.';
        $url = $isRejected
            ? route('payments.rejected', $this->payment->id)
            : ($orderId ? route('orders.show', $orderId) : route('payments.index'));

        return [
            'payment_id' => $this->payment->id,
            'message' => $message,
            'url' => $url,
        ];
    }
}

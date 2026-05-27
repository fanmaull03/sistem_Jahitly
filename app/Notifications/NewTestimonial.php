<?php

namespace App\Notifications;

use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTestimonial extends Notification
{
    use Queueable;

    protected Testimonial $testimonial;

    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'testimonial_id' => $this->testimonial->id,
            'order_id' => $this->testimonial->order_id,
            'customer_id' => $this->testimonial->customer_id,
            'message' => 'Testimoni baru masuk',
            'url' => route('admin.orders.show', $this->testimonial->order_id),
        ];
    }
}

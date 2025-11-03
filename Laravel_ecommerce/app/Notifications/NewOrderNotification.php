<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Both DB and email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Order Placed')
                    ->greeting('Hello Admin,')
                    ->line('A new order (#' . $this->order->id . ') has been placed.')
                    ->action('View Order', url(route('admin.order.show', $this->order->id)))
                    ->line('Thank you!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'A new order has been placed!',
            'user_id' => $this->order->user_id,
        ];
    }
}


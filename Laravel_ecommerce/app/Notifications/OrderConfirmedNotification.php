<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderConfirmedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Order is Confirmed')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your order (#' . $this->order->id . ') has been confirmed by the admin.')
                    ->action('View Order', url(route('orders.show', $this->order->id)))
                    ->line('Thank you for shopping with us!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Your order has been confirmed!',
        ];
    }
}

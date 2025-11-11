<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Order;
use App\Notifications\OrderConfirmedNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class OrderConfirmedNotificationTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_sends_notification_via_database_and_mail()
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $user->notify(new OrderConfirmedNotification($order));

        Notification::assertSentTo(
            [$user],
            OrderConfirmedNotification::class,
            function ($notification, $channels) use ($order) {
                return in_array('database', $channels) && in_array('mail', $channels);
            }
        );
    }

    
    public function test_it_has_correct_database_payload()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $notification = new OrderConfirmedNotification($order);
        $data = $notification->toDatabase($user);

        $this->assertArrayHasKey('order_id', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals($order->id, $data['order_id']);
        $this->assertEquals('Your order has been confirmed!', $data['message']);
    }

    
    public function test_it_has_correct_mail_message()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $notification = new OrderConfirmedNotification($order);
        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertStringContainsString('Your Order is Confirmed', $mailMessage->subject);
        $this->assertStringContainsString('Your order (#'.$order->id.') has been confirmed', $mailMessage->introLines[0]);
        $this->assertStringContainsString(route('orders.show', $order->id), $mailMessage->actionUrl);
    }
}

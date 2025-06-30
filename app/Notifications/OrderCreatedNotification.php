<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
{
    use Queueable;
    private $order;
    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        //
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم استلام طلب جديد')
            ->greeting('مرحباً!')
            ->line('تم إنشاء طلب جديد برقم #' . $this->order->id)
            ->action('عرض الطلب', url('/orders/' . $this->order->id))
            ->line('شكراً لاستخدامك منصتنا!');
    }



    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'طلب جديد',
            'body' => 'تم استلام طلب رقم #' . $this->order->id,
            'type' => 'order',
            'order_id' => $this->order->id,
        ];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

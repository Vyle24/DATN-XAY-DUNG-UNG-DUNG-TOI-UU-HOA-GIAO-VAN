<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $statusStr = '';
        if ($this->order->status === 'delivered') $statusStr = 'đã được giao thành công';
        elseif ($this->order->status === 'failed') $statusStr = 'giao hàng thất bại';
        elseif ($this->order->status === 'processing') $statusStr = 'đang được giao đến bạn';

        return [
            'order_id'   => $this->order->id,
            'order_code' => $this->order->order_code,
            'title'      => 'Cập nhật trạng thái đơn hàng',
            'message'    => 'Đơn hàng ' . $this->order->order_code . ' ' . $statusStr . '.',
            'type'       => 'status_update',
        ];
    }
}

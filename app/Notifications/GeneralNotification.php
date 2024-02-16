<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;
    protected $subject;
    protected $greeting;
    protected $message;
    protected $actionText;
    protected $actionUrl;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = $data['subject'] ?? null;
        $this->greeting = $data['greeting'] ?? null;
        $this->message = $data['message'] ?? null;
        $this->actionText = $data['actionText'] ?? null;
        $this->actionUrl = $data['actionUrl'] ?? null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->subject)
                    ->greeting($this->greeting)
                    ->line($this->message)
                    ->action($this->actionText, url($this->actionUrl))
                    ->line('AtounAfrica n\'est tout simplement pas le même sans vous!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

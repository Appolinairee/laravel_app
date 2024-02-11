<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCreatorNotification extends Notification
{
    use Queueable;
    private $creator_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($creator_name)
    {
        $this->creator_name = $creator_name;
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
                    ->subject("Un nouveau créateur MIA!")
                    ->greeting("Bonjour ". $notifiable->name)
                    ->line('Une nouvelle demande de création de compte créateur a été soumise de l\'utilisateur.'. $this->creator_name . '.')
                    ->action('Voir la demande', url('/dashboard'))
                    ->line('Merci de gérer cette demande.');
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

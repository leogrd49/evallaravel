<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WeeklyPlanningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Données hebdomadaires pour la notification
     * 
     * @var array<string, mixed>
     */
    protected array $weeklyData;

    /**
     * Create a new notification instance.
     *
     * @param array<string, mixed> $weeklyData
     * @return void
     */
    public function __construct(array $weeklyData)
    {
        $this->weeklyData = $weeklyData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Weekly Planning Summary')
            ->line('Here is your weekly planning summary.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'weekly_data' => $this->weeklyData
        ];
    }
}

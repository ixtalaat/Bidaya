<?php

namespace App\Notifications;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentAbsent extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Session $session, public string $studentName) {}

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
        $date = $this->session->date->toDateString();

        return [
            'type' => 'attendance.absent',
            'message_en' => "{$this->studentName} was marked absent for {$this->session->group->name} on {$date}.",
            'message_ar' => "تم تسجيل {$this->studentName} غائبًا في {$this->session->group->name} بتاريخ {$date}.",
            'url' => route('sessions.show', $this->session),
        ];
    }
}

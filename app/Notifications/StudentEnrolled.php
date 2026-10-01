<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentEnrolled extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Enrollment $enrollment) {}

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
        return [
            'type' => 'enrollment.created',
            'message_en' => "{$this->enrollment->student->name} was enrolled in {$this->enrollment->group->name}.",
            'message_ar' => "تم تسجيل {$this->enrollment->student->name} في {$this->enrollment->group->name}.",
            'url' => route('enrollments.show', $this->enrollment),
        ];
    }
}

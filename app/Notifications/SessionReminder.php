<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SessionReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  array<int, array{group: string, time: string}>  $sessions
     */
    public function __construct(public array $sessions, public string $date) {}

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
        $count = count($this->sessions);
        $lines = collect($this->sessions)
            ->map(fn (array $session): string => "{$session['group']} ({$session['time']})")
            ->join(', ');

        return [
            'type' => 'session.reminder',
            'message_en' => "You have {$count} session(s) today ({$this->date}): {$lines}.",
            'message_ar' => "لديك {$count} حصص اليوم ({$this->date}): {$lines}.",
            'url' => route('sessions.index', ['date' => $this->date]),
        ];
    }
}

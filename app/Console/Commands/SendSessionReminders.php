<?php

namespace App\Console\Commands;

use App\Enums\SessionStatus;
use App\Models\Session;
use App\Notifications\SessionReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Option;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-session-reminders')]
#[Description('Notify teachers about their scheduled sessions for the day')]
class SendSessionReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(
        #[Option('date', description: 'The date to remind about (Y-m-d), defaults to today')]
        ?string $date = null,
    ): int {
        $date ??= today()->toDateString();

        $sessions = Session::whereDate('date', $date)
            ->where('status', SessionStatus::Scheduled)
            ->with(['group:id,name', 'teacher.user'])
            ->orderBy('teacher_id')
            ->orderBy('start_time')
            ->get()
            ->groupBy('teacher_id');

        $notified = 0;

        foreach ($sessions as $teacherSessions) {
            $teacher = $teacherSessions->first()->teacher;

            if ($teacher?->user === null) {
                continue;
            }

            $teacher->user->notify(new SessionReminder(
                $teacherSessions->map(fn (Session $session): array => [
                    'group' => $session->group->name,
                    'time' => substr($session->start_time, 0, 5),
                ])->all(),
                $date
            ));

            $notified++;
        }

        $this->info("Session reminders sent to {$notified} teacher(s) for {$date}.");

        return self::SUCCESS;
    }
}

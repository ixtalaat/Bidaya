<?php

namespace App\Actions\Assignments;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RecordAssignmentGradesAction
{
    /**
     * Grade submissions for an assignment in a single atomic upsert.
     *
     * @param  array<int, array{enrollment_id: int|string, score: float|string, feedback?: ?string}>  $records
     *
     * @throws ValidationException
     */
    public function handle(Assignment $assignment, array $records): int
    {
        $activeEnrollmentIds = $assignment->group->enrollments()->active()->pluck('enrollments.id')->all();
        $maxScore = (float) $assignment->max_score;

        foreach ($records as $index => $record) {
            if (! in_array((int) $record['enrollment_id'], $activeEnrollmentIds, true)) {
                throw ValidationException::withMessages([
                    "records.{$index}.enrollment_id" => __('assignments.unknown_enrollment'),
                ]);
            }

            if ((float) $record['score'] > $maxScore) {
                throw ValidationException::withMessages([
                    "records.{$index}.score" => __('assignments.score_above_max', ['max' => $assignment->max_score]),
                ]);
            }
        }

        $now = Carbon::now()->toDateTimeString();

        $rows = array_map(fn (array $record): array => [
            'assignment_id' => $assignment->id,
            'enrollment_id' => (int) $record['enrollment_id'],
            'score' => $record['score'],
            'feedback' => $record['feedback'] ?? null,
            'status' => SubmissionStatus::Reviewed->value,
            'created_at' => $now,
            'updated_at' => $now,
        ], $records);

        Submission::upsert($rows, ['assignment_id', 'enrollment_id'], ['score', 'feedback', 'status', 'updated_at']);

        return count($rows);
    }
}

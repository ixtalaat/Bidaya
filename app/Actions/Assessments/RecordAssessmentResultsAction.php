<?php

namespace App\Actions\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RecordAssessmentResultsAction
{
    /**
     * Record grades for an assessment in a single atomic upsert.
     *
     * @param  array<int, array{enrollment_id: int|string, score: float|string, feedback?: ?string}>  $records
     *
     * @throws ValidationException
     */
    public function handle(Assessment $assessment, array $records): int
    {
        $activeEnrollmentIds = $assessment->group->enrollments()->active()->pluck('enrollments.id')->all();
        $maxScore = (float) $assessment->max_score;

        foreach ($records as $index => $record) {
            if (! in_array((int) $record['enrollment_id'], $activeEnrollmentIds, true)) {
                throw ValidationException::withMessages([
                    "records.{$index}.enrollment_id" => __('assessments.unknown_enrollment'),
                ]);
            }

            if ((float) $record['score'] > $maxScore) {
                throw ValidationException::withMessages([
                    "records.{$index}.score" => __('assessments.score_above_max', ['max' => $assessment->max_score]),
                ]);
            }
        }

        $now = Carbon::now()->toDateTimeString();

        $rows = array_map(fn (array $record): array => [
            'assessment_id' => $assessment->id,
            'enrollment_id' => (int) $record['enrollment_id'],
            'score' => $record['score'],
            'feedback' => $record['feedback'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $records);

        AssessmentResult::upsert($rows, ['assessment_id', 'enrollment_id'], ['score', 'feedback', 'updated_at']);

        return count($rows);
    }
}

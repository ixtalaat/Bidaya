<?php

namespace App\Actions\Assignments;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RecordSubmissionAction
{
    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws ValidationException
     */
    public function handle(Assignment $assignment, Enrollment $enrollment, array $validated, User $submitter): Submission
    {
        if ($enrollment->group_id !== $assignment->group_id) {
            throw ValidationException::withMessages([
                'enrollment_id' => __('submissions.wrong_group'),
            ]);
        }

        if (! $enrollment->isActive()) {
            throw ValidationException::withMessages([
                'enrollment_id' => __('submissions.enrollment_not_active'),
            ]);
        }

        if (! $assignment->isOpenForSubmissions()) {
            throw ValidationException::withMessages([
                'assignment' => __('submissions.assignment_closed'),
            ]);
        }

        return DB::transaction(function () use ($assignment, $enrollment, $validated, $submitter): Submission {
            $submission = Submission::firstOrNew([
                'assignment_id' => $assignment->id,
                'enrollment_id' => $enrollment->id,
            ]);

            $file = $validated['file'] ?? null;
            unset($validated['file']);

            if ($file instanceof UploadedFile) {
                if ($submission->file_path !== null) {
                    Storage::disk('public')->delete($submission->file_path);
                }

                $submission->file_path = $file->store('submission-files', 'public');
            }

            $submission->fill([
                'answer' => $validated['answer'] ?? $submission->answer,
                'submitted_by' => $submitter->id,
                'submitted_at' => Carbon::now(),
                'status' => $this->resolveStatus($assignment),
            ]);

            if ($submission->exists) {
                $submission->score = null;
                $submission->feedback = null;
            }

            $submission->save();

            return $submission->refresh();
        });
    }

    private function resolveStatus(Assignment $assignment): SubmissionStatus
    {
        return today()->gt($assignment->due_date) ? SubmissionStatus::Late : SubmissionStatus::Submitted;
    }
}

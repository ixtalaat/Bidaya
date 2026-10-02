<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentStatus;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IssueCertificateAction
{
    public function __construct(private AuditLogger $audit) {}

    /**
     * @throws ValidationException
     */
    public function handle(Enrollment $enrollment, User $issuer): Certificate
    {
        if ($enrollment->status !== EnrollmentStatus::Completed) {
            throw ValidationException::withMessages([
                'enrollment' => __('certificates.only_completed'),
            ]);
        }

        if ($enrollment->certificate()->exists()) {
            throw ValidationException::withMessages([
                'enrollment' => __('certificates.already_issued'),
            ]);
        }

        return DB::transaction(function () use ($enrollment, $issuer): Certificate {
            $year = Carbon::now()->format('Y');
            $sequence = Certificate::whereYear('created_at', $year)->lockForUpdate()->count() + 1;

            $certificate = Certificate::create([
                'enrollment_id' => $enrollment->id,
                'number' => sprintf('BID-%s-%04d', $year, $sequence),
                'issued_by' => $issuer->id,
                'issued_at' => Carbon::now(),
            ]);

            $this->audit->log($issuer, 'certificate.issued', $certificate, [], $certificate->toArray());

            return $certificate;
        });
    }
}

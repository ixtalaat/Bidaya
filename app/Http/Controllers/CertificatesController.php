<?php

namespace App\Http\Controllers;

use App\Actions\Enrollment\IssueCertificateAction;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CertificatesController extends Controller
{
    public function show(Certificate $certificate): View
    {
        Gate::authorize('view', $certificate);

        $certificate->load(['enrollment.student', 'enrollment.group.course', 'issuer']);

        return view('certificates.show', ['certificate' => $certificate]);
    }

    public function store(Request $request, Enrollment $enrollment, IssueCertificateAction $issue): RedirectResponse
    {
        Gate::authorize('issue', [Certificate::class, $enrollment]);

        $certificate = $issue->handle($enrollment, $request->user());

        return redirect()->route('certificates.show', $certificate)->with('status', __('certificates.issued'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\GroupStatus;
use App\Enums\StudentStatus;
use App\Enums\TeacherStatus;
use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'stats' => [
                'courses' => Course::where('status', CourseStatus::Active)->count(),
                'groups' => Group::where('status', GroupStatus::Active)->count(),
                'teachers' => Teacher::where('status', TeacherStatus::Active)->count(),
                'students' => Student::withStatus(StudentStatus::Active)->count(),
            ],
            'featuredCourses' => Course::where('status', CourseStatus::Active)
                ->withCount('groups')
                ->orderBy('name')
                ->limit(6)
                ->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function sendContact(StoreContactRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return redirect()->route('contact')->with('status', __('pages.message_sent'));
    }
}

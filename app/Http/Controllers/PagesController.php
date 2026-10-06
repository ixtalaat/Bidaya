<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\GalleryType;
use App\Enums\GroupStatus;
use App\Enums\StudentStatus;
use App\Enums\TeacherStatus;
use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\GalleryItem;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

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
            'galleryPhotos' => GalleryItem::published()
                ->where('type', GalleryType::Photo)
                ->whereNotNull('photo_path')
                ->orderByDesc('id')
                ->limit(4)
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

    public function courses(): View
    {
        return view('pages.courses', [
            'courses' => Course::whereIn('status', [CourseStatus::Published, CourseStatus::Active])
                ->withCount('groups')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function courseDetails(Course $course): View
    {
        abort_unless($course->status->acceptsEnrollments(), 404);

        $course->load([
            'groups' => fn ($query) => $query->where('status', GroupStatus::Active)->orderBy('name'),
            'groups.teacher.user',
            'groups.schedules',
        ]);

        return view('pages.course-details', ['course' => $course]);
    }

    public function gallery(Request $request): View
    {
        $type = GalleryType::tryFrom($request->string('type')->toString());

        $items = GalleryItem::published()
            ->when($type, fn ($query, GalleryType $type) => $query->where('type', $type))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.gallery', ['items' => $items, 'activeType' => $type]);
    }

    public function sendContact(StoreContactRequest $request): RedirectResponse
    {
        $message = ContactMessage::create($request->validated());

        try {
            Mail::to(config('services.bidaya.contact_email'))->send(new ContactMessageReceived($message));
        } catch (Throwable $exception) {
            Log::warning('Contact email failed to send.', ['error' => $exception->getMessage()]);
        }

        return redirect()->route('contact')->with('status', __('pages.message_sent'));
    }
}

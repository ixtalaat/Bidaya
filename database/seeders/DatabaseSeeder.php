<?php

namespace Database\Seeders;

use App\Actions\Enrollment\EnrollStudentAction;
use App\Actions\Payments\RecordPaymentAction;
use App\Actions\Sessions\UpdateSessionStatusAction;
use App\Enums\CourseStatus;
use App\Enums\DayOfWeek;
use App\Enums\GroupStatus;
use App\Enums\PaymentMethod;
use App\Enums\RelationshipType;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Enums\StudentStatus;
use App\Enums\TeacherStatus;
use App\Models\Course;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->withRole(Role::SuperAdmin)->create([
            'name' => 'Admin',
            'email' => config('services.bidaya.admin_email'),
            'password' => config('services.bidaya.admin_password'),
        ]);

        User::factory()->withRole(Role::Manager)->create([
            'name' => 'Manager',
            'email' => 'manager@bidaya.test',
        ]);

        $accountant = User::factory()->withRole(Role::Accountant)->create([
            'name' => 'Accountant',
            'email' => 'accountant@bidaya.test',
        ]);

        $teacherUser = User::factory()->withRole(Role::Teacher)->create([
            'name' => 'Sara Ahmed',
            'email' => 'teacher@bidaya.test',
        ]);

        $teacher = Teacher::factory()->create([
            'user_id' => $teacherUser->id,
            'specialization' => 'English',
            'status' => TeacherStatus::Active,
        ]);

        $parentUser = User::factory()->withRole(Role::Parent)->create([
            'name' => 'Ahmed Mohamed',
            'email' => 'parent@bidaya.test',
        ]);

        $guardian = Guardian::factory()->create([
            'user_id' => $parentUser->id,
            'name' => 'Ahmed Mohamed',
            'phone' => '01001234567',
        ]);

        $student = Student::factory()->create([
            'name' => 'Omar Ahmed',
            'status' => StudentStatus::Active,
        ]);

        $guardian->students()->attach($student, ['relationship' => RelationshipType::Father->value]);

        $course = Course::factory()->create([
            'name' => 'English for Kids',
            'category' => 'Language',
            'monthly_fee' => 2000,
            'status' => CourseStatus::Active,
        ]);

        $group = $course->groups()->create([
            'teacher_id' => $teacher->id,
            'name' => 'English Group A',
            'capacity' => 15,
            'classroom' => 'Room 3',
            'start_date' => today()->subMonth()->toDateString(),
            'status' => GroupStatus::Active,
        ]);

        $group->schedules()->create([
            'day_of_week' => DayOfWeek::Sunday,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $group->schedules()->create([
            'day_of_week' => DayOfWeek::Tuesday,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $enrollment = app(EnrollStudentAction::class)->handle([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->subWeeks(2)->toDateString(),
            'start_date' => today()->subWeeks(2)->toDateString(),
            'discount_amount' => 200,
        ]);

        app(RecordPaymentAction::class)->handle([
            'invoice_id' => $enrollment->invoices()->firstOrFail()->id,
            'amount' => 1000,
            'payment_date' => today()->subWeek()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ], $accountant);

        $session = $group->sessions()->create([
            'teacher_id' => $teacher->id,
            'date' => today()->toDateString(),
            'start_time' => '17:00',
            'end_time' => '18:00',
            'classroom' => 'Room 3',
            'status' => SessionStatus::Scheduled,
        ]);

        $session->attendances()->create([
            'enrollment_id' => $enrollment->id,
            'status' => 'present',
            'recorded_by' => $teacherUser->id,
        ]);

        app(UpdateSessionStatusAction::class)->handle($session, [
            'status' => SessionStatus::Completed->value,
            'teacher_notes' => 'Introduced new vocabulary set.',
        ]);

        $assessment = $group->assessments()->create([
            'name' => 'Quiz 1',
            'type' => 'quiz',
            'max_score' => 20,
            'assessment_date' => today()->subDays(2)->toDateString(),
        ]);

        $assessment->results()->create([
            'enrollment_id' => $enrollment->id,
            'score' => 18,
            'feedback' => 'Strong vocabulary improvement.',
        ]);

        $this->command->info('Demo logins (password: password): admin, teacher and parent accounts seeded.');
    }
}

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

        $arabicTeacherUser = User::factory()->withRole(Role::Teacher)->create([
            'name' => 'Sara Ahmed',
            'email' => 'teacher@bidaya.test',
        ]);

        $arabicTeacher = Teacher::factory()->create([
            'user_id' => $arabicTeacherUser->id,
            'specialization' => 'Arabic',
            'status' => TeacherStatus::Active,
        ]);

        $mathTeacherUser = User::factory()->withRole(Role::Teacher)->create([
            'name' => 'Mohamed Ali',
            'email' => 'math.teacher@bidaya.test',
        ]);

        $mathTeacher = Teacher::factory()->create([
            'user_id' => $mathTeacherUser->id,
            'specialization' => 'Math',
            'status' => TeacherStatus::Active,
        ]);

        $nurseryTeacherUser = User::factory()->withRole(Role::Teacher)->create([
            'name' => 'Nourhan Khaled',
            'email' => 'nursery.teacher@bidaya.test',
        ]);

        $nurseryTeacher = Teacher::factory()->create([
            'user_id' => $nurseryTeacherUser->id,
            'specialization' => 'Nursery',
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

        $foundation = Course::factory()->create([
            'name' => 'دروس التأسيس',
            'description' => 'Foundation lessons in Arabic and Math, 12 sessions per month.',
            'category' => 'Foundation',
            'min_age' => 4,
            'max_age' => 12,
            'sessions_count' => 12,
            'monthly_fee' => 350,
            'status' => CourseStatus::Active,
        ]);

        $arabicGroup = $foundation->groups()->create([
            'teacher_id' => $arabicTeacher->id,
            'name' => 'مجموعة العربي',
            'capacity' => 15,
            'classroom' => 'Room 1',
            'start_date' => today()->subMonth()->toDateString(),
            'status' => GroupStatus::Active,
        ]);

        $arabicGroup->schedules()->createMany([
            ['day_of_week' => DayOfWeek::Sunday, 'start_time' => '17:00', 'end_time' => '18:00'],
            ['day_of_week' => DayOfWeek::Tuesday, 'start_time' => '17:00', 'end_time' => '18:00'],
        ]);

        $mathGroup = $foundation->groups()->create([
            'teacher_id' => $mathTeacher->id,
            'name' => 'مجموعة الحساب',
            'capacity' => 15,
            'classroom' => 'Room 2',
            'start_date' => today()->subMonth()->toDateString(),
            'status' => GroupStatus::Active,
        ]);

        $mathGroup->schedules()->createMany([
            ['day_of_week' => DayOfWeek::Monday, 'start_time' => '17:00', 'end_time' => '18:00'],
            ['day_of_week' => DayOfWeek::Wednesday, 'start_time' => '17:00', 'end_time' => '18:00'],
        ]);

        $nursery = Course::factory()->create([
            'name' => 'الحضانة',
            'description' => 'Daily nursery: Arabic, Math, Quran and Aqeedah, Saturday to Thursday from 9:00 AM to 12:00 PM.',
            'category' => 'Nursery',
            'min_age' => 3,
            'max_age' => 6,
            'monthly_fee' => 350,
            'status' => CourseStatus::Active,
        ]);

        $nurseryGroup = $nursery->groups()->create([
            'teacher_id' => $nurseryTeacher->id,
            'name' => 'فصل الحضانة الصباحي',
            'capacity' => 20,
            'classroom' => 'Nursery Room',
            'start_date' => today()->subMonth()->toDateString(),
            'status' => GroupStatus::Active,
        ]);

        $nurseryGroup->schedules()->createMany([
            ['day_of_week' => DayOfWeek::Saturday, 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => DayOfWeek::Sunday, 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => DayOfWeek::Monday, 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => DayOfWeek::Tuesday, 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => DayOfWeek::Wednesday, 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => DayOfWeek::Thursday, 'start_time' => '09:00', 'end_time' => '12:00'],
        ]);

        $group = $arabicGroup;

        $enrollment = app(EnrollStudentAction::class)->handle([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->subWeeks(2)->toDateString(),
            'start_date' => today()->subWeeks(2)->toDateString(),
            'discount_amount' => 0,
        ]);

        app(RecordPaymentAction::class)->handle([
            'invoice_id' => $enrollment->invoices()->firstOrFail()->id,
            'amount' => 200,
            'payment_date' => today()->subWeek()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ], $accountant);

        app(EnrollStudentAction::class)->handle([
            'student_id' => $student->id,
            'group_id' => $mathGroup->id,
            'enrollment_date' => today()->subWeeks(2)->toDateString(),
            'start_date' => today()->subWeeks(2)->toDateString(),
            'discount_amount' => 0,
        ]);

        $session = $group->sessions()->create([
            'teacher_id' => $arabicTeacher->id,
            'date' => today()->toDateString(),
            'start_time' => '17:00',
            'end_time' => '18:00',
            'classroom' => 'Room 1',
            'status' => SessionStatus::Scheduled,
        ]);

        $session->attendances()->create([
            'enrollment_id' => $enrollment->id,
            'status' => 'present',
            'recorded_by' => $arabicTeacherUser->id,
        ]);

        app(UpdateSessionStatusAction::class)->handle($session, [
            'status' => SessionStatus::Completed->value,
            'teacher_notes' => 'Practiced reading and new words.',
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
            'feedback' => 'Good reading progress.',
        ]);

        $this->command->info('Demo logins (password: password): admin, teacher and parent accounts seeded.');
    }
}

<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\RelationshipType;
use App\Enums\Role;
use App\Enums\StudentStatus;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('students.index'))->assertRedirect(route('login'));
    }

    public function test_teachers_cannot_manage_students(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('students.index'))->assertForbidden();
        $this->actingAs($teacher)->post(route('students.store'), [])->assertForbidden();
    }

    public function test_managers_can_list_and_filter_students(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        Student::factory()->create(['name' => 'Omar Ahmed', 'status' => StudentStatus::Active]);
        Student::factory()->create(['name' => 'Jana Ali', 'status' => StudentStatus::Suspended]);

        $response = $this->actingAs($manager)->get(route('students.index'));

        $response->assertOk();
        $response->assertSee('Omar Ahmed');
        $response->assertSee('Jana Ali');

        $filtered = $this->actingAs($manager)->get(route('students.index', [
            'search' => 'Omar',
            'status' => StudentStatus::Suspended->value,
        ]));

        $filtered->assertOk();
        $filtered->assertDontSee('Omar Ahmed');
        $filtered->assertDontSee('Jana Ali');
    }

    public function test_managers_can_create_a_student_with_guardians(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $father = Guardian::factory()->create();
        $mother = Guardian::factory()->create();

        $response = $this->actingAs($manager)->post(route('students.store'), [
            'name' => 'Youssef Ahmed',
            'date_of_birth' => '2018-05-10',
            'gender' => Gender::Male->value,
            'status' => StudentStatus::Active->value,
            'parents' => [
                ['id' => $father->id, 'relationship' => RelationshipType::Father->value],
                ['id' => $mother->id, 'relationship' => RelationshipType::Mother->value],
            ],
        ]);

        $student = Student::where('name', 'Youssef Ahmed')->firstOrFail();
        $response->assertRedirect(route('students.show', $student));

        $this->assertCount(2, $student->guardians);
        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $father->id,
            'student_id' => $student->id,
            'relationship' => RelationshipType::Father->value,
        ]);
    }

    public function test_student_creation_rolls_back_when_a_guardian_is_invalid(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('students.store'), [
            'name' => 'Broken Link',
            'date_of_birth' => '2018-05-10',
            'gender' => Gender::Male->value,
            'status' => StudentStatus::Active->value,
            'parents' => [
                ['id' => 999999, 'relationship' => RelationshipType::Father->value],
            ],
        ]);

        $response->assertSessionHasErrors('parents.0.id');
        $this->assertDatabaseMissing('students', ['name' => 'Broken Link']);
    }

    public function test_managers_can_update_student_guardians(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $student = Student::factory()->create();
        $oldGuardian = Guardian::factory()->create();
        $newGuardian = Guardian::factory()->create();
        $student->guardians()->attach($oldGuardian, ['relationship' => RelationshipType::Father->value]);

        $response = $this->actingAs($manager)->put(route('students.update', $student), [
            'name' => $student->name,
            'date_of_birth' => $student->date_of_birth->toDateString(),
            'gender' => $student->gender->value,
            'status' => StudentStatus::Suspended->value,
            'parents' => [
                ['id' => $newGuardian->id, 'relationship' => RelationshipType::Mother->value],
            ],
        ]);

        $response->assertRedirect(route('students.show', $student));

        $fresh = $student->fresh();
        $this->assertSame(StudentStatus::Suspended, $fresh->status);
        $this->assertTrue($fresh->guardians->contains($newGuardian));
        $this->assertFalse($fresh->guardians->contains($oldGuardian));
    }

    public function test_student_photo_is_stored_and_removed_with_the_student(): void
    {
        Storage::fake('public');
        $manager = User::factory()->withRole(Role::Manager)->create();

        $createResponse = $this->actingAs($manager)->post(route('students.store'), [
            'name' => 'Photo Kid',
            'date_of_birth' => '2019-01-01',
            'gender' => Gender::Female->value,
            'status' => StudentStatus::Active->value,
            'photo' => UploadedFile::fake()->image('kid.jpg'),
        ]);

        $student = Student::where('name', 'Photo Kid')->firstOrFail();
        $createResponse->assertRedirect(route('students.show', $student));
        Storage::disk('public')->assertExists($student->photo_path);

        $this->actingAs($manager)->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'));

        Storage::disk('public')->assertMissing($student->photo_path);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_only_active_students_are_enrollable(): void
    {
        $active = Student::factory()->create(['status' => StudentStatus::Active]);
        $suspended = Student::factory()->create(['status' => StudentStatus::Suspended]);

        $this->assertTrue($active->isEnrollable());
        $this->assertFalse($suspended->isEnrollable());
    }
}

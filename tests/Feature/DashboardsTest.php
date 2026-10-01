<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Session;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_admins_see_center_metrics(): void
    {
        $admin = User::factory()->withRole(Role::SuperAdmin)->create();
        Student::factory()->create(['name' => 'Omar Ahmed']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(__('dashboard.admin_title'));
    }

    public function test_teachers_see_only_their_own_sessions(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $ownGroup = Group::factory()->create(['teacher_id' => $teacher->id, 'name' => 'Own Group']);
        $otherGroup = Group::factory()->create(['teacher_id' => $otherTeacher->id, 'name' => 'Other Group']);
        Session::factory()->create([
            'group_id' => $ownGroup->id,
            'teacher_id' => $teacher->id,
            'date' => today()->toDateString(),
        ]);
        Session::factory()->create([
            'group_id' => $otherGroup->id,
            'teacher_id' => $otherTeacher->id,
            'date' => today()->toDateString(),
        ]);

        $response = $this->actingAs($teacher->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Own Group');
        $response->assertDontSee('Other Group');
    }

    public function test_unlinked_teachers_see_guidance(): void
    {
        $teacherUser = User::factory()->withRole(Role::Teacher)->create();

        $response = $this->actingAs($teacherUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(__('dashboard.unlinked_title'));
    }

    public function test_linked_parents_see_their_children(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create(['name' => 'Omar Ahmed']);
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create();
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $response = $this->actingAs($parentUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Omar Ahmed');
        $response->assertSee($group->course->name);
    }

    public function test_unlinked_parents_see_guidance(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();

        $response = $this->actingAs($parentUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(__('dashboard.unlinked_title'));
        $response->assertDontSee(__('dashboard.parent_title'));
    }

    public function test_accountants_see_financial_summary(): void
    {
        $accountant = User::factory()->withRole(Role::Accountant)->create();

        $response = $this->actingAs($accountant)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(__('dashboard.accountant_title'));
    }

    public function test_managers_can_link_a_parent_account(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create();
        $parentUser = User::factory()->withRole(Role::Parent)->create();

        $response = $this->actingAs($manager)->put(route('parents.update', $guardian), [
            'name' => $guardian->name,
            'phone' => $guardian->phone,
            'user_id' => $parentUser->id,
        ]);

        $response->assertRedirect(route('parents.show', $guardian));
        $this->assertSame($parentUser->id, $guardian->fresh()->user_id);
    }

    public function test_non_parent_accounts_cannot_be_linked(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create();
        $teacherUser = User::factory()->withRole(Role::Teacher)->create();

        $response = $this->actingAs($manager)->put(route('parents.update', $guardian), [
            'name' => $guardian->name,
            'phone' => $guardian->phone,
            'user_id' => $teacherUser->id,
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertNull($guardian->fresh()->user_id);
    }

    public function test_admin_nav_shows_quick_create_and_role_badge(): void
    {
        $admin = User::factory()->withRole(Role::SuperAdmin)->create();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(__('layout.quick_create'));
        $response->assertSee(route('students.create'));
        $response->assertSee($admin->role->label());
    }

    public function test_teacher_nav_hides_quick_create(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs($teacher->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee(__('layout.quick_create'));
        $response->assertSee($teacher->user->role->label());
    }
}

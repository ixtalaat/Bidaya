<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('courses.index'))->assertRedirect(route('login'));
    }

    public function test_teachers_cannot_manage_courses(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('courses.index'))->assertForbidden();
    }

    public function test_managers_can_list_and_filter_courses(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        Course::factory()->create(['name' => 'English Kids', 'status' => CourseStatus::Active]);
        Course::factory()->create(['name' => 'Math Draft', 'status' => CourseStatus::Draft]);

        $response = $this->actingAs($manager)->get(route('courses.index'));

        $response->assertOk();
        $response->assertSee('English Kids');

        $filtered = $this->actingAs($manager)->get(route('courses.index', [
            'status' => CourseStatus::Draft->value,
        ]));

        $filtered->assertOk();
        $filtered->assertSee('Math Draft');
        $filtered->assertDontSee('English Kids');
    }

    public function test_managers_can_create_a_course(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('courses.store'), [
            'name' => 'Science Juniors',
            'monthly_fee' => 2500,
            'status' => CourseStatus::Published->value,
            'min_age' => 6,
            'max_age' => 10,
        ]);

        $course = Course::where('name', 'Science Juniors')->firstOrFail();
        $response->assertRedirect(route('courses.show', $course));
        $this->assertDatabaseHas('courses', ['name' => 'Science Juniors']);
    }

    public function test_max_age_must_not_be_below_min_age(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('courses.store'), [
            'name' => 'Broken Ages',
            'monthly_fee' => 1000,
            'status' => CourseStatus::Draft->value,
            'min_age' => 10,
            'max_age' => 6,
        ]);

        $response->assertSessionHasErrors('max_age');
        $this->assertDatabaseMissing('courses', ['name' => 'Broken Ages']);
    }

    public function test_courses_with_groups_cannot_be_deleted(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->hasGroups(1)->create();

        $response = $this->actingAs($manager)->delete(route('courses.destroy', $course));

        $response->assertSessionHasErrors('course');
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_courses_without_groups_can_be_deleted(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create();

        $this->actingAs($manager)->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}

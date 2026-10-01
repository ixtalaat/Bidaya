<?php

namespace Tests\Feature;

use App\Enums\DayOfWeek;
use App\Enums\Role;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $group = Group::factory()->create();

        $this->get(route('groups.schedules.index', $group))->assertRedirect(route('login'));
    }

    public function test_teachers_cannot_manage_schedules(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();
        $group = Group::factory()->create();

        $this->actingAs($teacher)->get(route('groups.schedules.index', $group))->assertForbidden();
    }

    public function test_managers_can_add_a_weekly_slot(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($manager)->post(route('groups.schedules.store', $group), [
            'day_of_week' => DayOfWeek::Sunday->value,
            'start_time' => '17:00',
            'end_time' => '18:00',
            'classroom' => 'Room 3',
        ]);

        $response->assertRedirect(route('groups.schedules.index', $group));
        $this->assertDatabaseHas('group_schedules', [
            'group_id' => $group->id,
            'day_of_week' => DayOfWeek::Sunday->value,
        ]);
    }

    public function test_duplicate_slots_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        GroupSchedule::factory()->create([
            'group_id' => $group->id,
            'day_of_week' => DayOfWeek::Tuesday,
            'start_time' => '17:00',
        ]);

        $response = $this->actingAs($manager)->post(route('groups.schedules.store', $group), [
            'day_of_week' => DayOfWeek::Tuesday->value,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $response->assertSessionHasErrors('day_of_week');
        $this->assertSame(1, $group->schedules()->count());
    }

    public function test_end_time_must_follow_start_time(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($manager)->post(route('groups.schedules.store', $group), [
            'day_of_week' => DayOfWeek::Monday->value,
            'start_time' => '18:00',
            'end_time' => '17:00',
        ]);

        $response->assertSessionHasErrors('end_time');
        $this->assertSame(0, $group->schedules()->count());
    }

    public function test_managers_can_update_and_delete_a_slot(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $schedule = GroupSchedule::factory()->create(['group_id' => $group->id]);

        $this->actingAs($manager)->put(route('groups.schedules.update', [$group, $schedule]), [
            'day_of_week' => DayOfWeek::Wednesday->value,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ])->assertRedirect(route('groups.schedules.index', $group));

        $this->assertSame(DayOfWeek::Wednesday, $schedule->fresh()->day_of_week);

        $this->actingAs($manager)->delete(route('groups.schedules.destroy', [$group, $schedule]))
            ->assertRedirect(route('groups.schedules.index', $group));

        $this->assertDatabaseMissing('group_schedules', ['id' => $schedule->id]);
    }
}

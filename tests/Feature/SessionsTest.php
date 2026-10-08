<?php

namespace Tests\Feature;

use App\Enums\DayOfWeek;
use App\Enums\GroupStatus;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Session;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_generation_expands_the_weekly_schedule(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create([
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
            'status' => GroupStatus::Active,
        ]);
        GroupSchedule::factory()->create([
            'group_id' => $group->id,
            'day_of_week' => DayOfWeek::Sunday,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);
        GroupSchedule::factory()->create([
            'group_id' => $group->id,
            'day_of_week' => DayOfWeek::Tuesday,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $response = $this->actingAs($manager)->post(route('sessions.generate.store', $group), [
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-14',
        ]);

        $response->assertRedirect(route('sessions.index', ['group_id' => $group->id]));
        // Sundays + Tuesdays between Oct 1-14: Oct 4, 6, 11, 13.
        $this->assertSame(4, $group->sessions()->count());
    }

    public function test_generation_skips_existing_sessions(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create([
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
            'status' => GroupStatus::Active,
        ]);
        GroupSchedule::factory()->create([
            'group_id' => $group->id,
            'day_of_week' => DayOfWeek::Sunday,
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);
        Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'date' => '2026-10-04',
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $this->actingAs($manager)->post(route('sessions.generate.store', $group), [
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-14',
        ]);

        // Oct 4 exists; only Oct 11 is new.
        $this->assertSame(2, $group->sessions()->count());
    }

    public function test_generation_is_blocked_for_cancelled_groups(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create(['status' => GroupStatus::Cancelled]);
        GroupSchedule::factory()->create(['group_id' => $group->id]);

        $response = $this->actingAs($manager)->post(route('sessions.generate.store', $group), [
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-14',
        ]);

        $response->assertSessionHasErrors('group');
        $this->assertSame(0, $group->sessions()->count());
    }

    public function test_teacher_overlaps_are_blocked_on_manual_create(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = Teacher::factory()->create();
        $firstGroup = Group::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => GroupStatus::Active,
            'start_date' => '2026-10-01',
        ]);
        $secondGroup = Group::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => GroupStatus::Active,
            'start_date' => '2026-10-01',
        ]);
        Session::factory()->create([
            'group_id' => $firstGroup->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-10-05',
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $response = $this->actingAs($manager)->post(route('sessions.store'), [
            'group_id' => $secondGroup->id,
            'date' => '2026-10-05',
            'start_time' => '17:30',
            'end_time' => '18:30',
        ]);

        $response->assertSessionHasErrors('start_time');
        $this->assertSame(0, $secondGroup->sessions()->count());
    }

    public function test_non_overlapping_slots_are_allowed(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = Teacher::factory()->create();
        $firstGroup = Group::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => GroupStatus::Active,
            'start_date' => '2026-10-01',
        ]);
        $secondGroup = Group::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => GroupStatus::Active,
            'start_date' => '2026-10-01',
            'classroom' => 'Room 9',
        ]);
        Session::factory()->create([
            'group_id' => $firstGroup->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-10-05',
            'start_time' => '17:00',
            'end_time' => '18:00',
            'classroom' => 'Room 1',
        ]);

        $response = $this->actingAs($manager)->post(route('sessions.store'), [
            'group_id' => $secondGroup->id,
            'date' => '2026-10-05',
            'start_time' => '18:00',
            'end_time' => '19:00',
        ]);

        $session = Session::where('group_id', $secondGroup->id)->firstOrFail();
        $response->assertRedirect(route('sessions.show', $session));
    }

    public function test_cancelled_sessions_do_not_block_new_ones(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = Teacher::factory()->create();
        $group = Group::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => GroupStatus::Active,
            'start_date' => '2026-10-01',
        ]);
        Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-10-05',
            'start_time' => '17:00',
            'end_time' => '18:00',
            'status' => SessionStatus::Cancelled,
        ]);

        $response = $this->actingAs($manager)->post(route('sessions.store'), [
            'group_id' => $group->id,
            'date' => '2026-10-05',
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame(2, $group->sessions()->count());
    }

    public function test_session_status_follows_allowed_transitions(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $session = Session::factory()->create(['status' => SessionStatus::Scheduled]);

        $this->actingAs($manager)->post(route('sessions.status', $session), [
            'status' => SessionStatus::Completed->value,
            'teacher_notes' => 'Covered unit 3.',
        ])->assertRedirect(route('sessions.show', $session));

        $fresh = $session->fresh();
        $this->assertSame(SessionStatus::Completed, $fresh->status);
        $this->assertSame('Covered unit 3.', $fresh->teacher_notes);

        $this->actingAs($manager)->post(route('sessions.status', $session), [
            'status' => SessionStatus::Scheduled->value,
        ])->assertSessionHasErrors('status');
    }

    public function test_teachers_only_see_their_own_sessions(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $ownSession = Session::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $teacher->id])->id,
            'teacher_id' => $teacher->id,
        ]);
        $otherSession = Session::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $otherTeacher->id])->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $teacherUser = $teacher->user;

        $index = $this->actingAs($teacherUser)->get(route('sessions.index'));
        $index->assertOk();

        $this->actingAs($teacherUser)->get(route('sessions.show', $ownSession))->assertOk();
        $this->actingAs($teacherUser)->get(route('sessions.show', $otherSession))->assertForbidden();
    }

    public function test_teachers_can_complete_their_sessions(): void
    {
        $teacher = Teacher::factory()->create();
        $session = Session::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $teacher->id])->id,
            'teacher_id' => $teacher->id,
            'status' => SessionStatus::Scheduled,
        ]);

        $this->actingAs($teacher->user)->post(route('sessions.status', $session), [
            'status' => SessionStatus::Completed->value,
        ])->assertRedirect(route('sessions.show', $session));

        $this->assertSame(SessionStatus::Completed, $session->fresh()->status);
    }

    public function test_completed_sessions_cannot_be_deleted(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $session = Session::factory()->create(['status' => SessionStatus::Completed]);

        $response = $this->actingAs($manager)->delete(route('sessions.destroy', $session));

        $response->assertSessionHasErrors('session');
        $this->assertDatabaseHas('class_sessions', ['id' => $session->id]);
    }

    public function test_completed_sessions_cannot_be_rescheduled(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $session = Session::factory()->create([
            'date' => '2026-10-05',
            'status' => SessionStatus::Completed,
        ]);

        $this->actingAs($manager)->get(route('sessions.edit', $session))->assertForbidden();

        $response = $this->actingAs($manager)->put(route('sessions.update', $session), [
            'date' => '2026-10-06',
            'start_time' => '17:00',
            'end_time' => '18:00',
        ]);

        $response->assertSessionHasErrors('session');
        $this->assertSame('2026-10-05', $session->fresh()->date->toDateString());
    }

    public function test_parents_and_accountants_cannot_list_sessions(): void
    {
        $parent = User::factory()->withRole(Role::Parent)->create();
        $accountant = User::factory()->withRole(Role::Accountant)->create();
        $studentUser = User::factory()->withRole(Role::Student)->create();

        $this->actingAs($parent)->get(route('sessions.index'))->assertForbidden();
        $this->actingAs($accountant)->get(route('sessions.index'))->assertForbidden();
        $this->actingAs($studentUser)->get(route('sessions.index'))->assertForbidden();
    }
}

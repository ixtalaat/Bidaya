<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Enums\DayOfWeek;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Group;
use App\Models\GroupSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_courses(): void
    {
        Course::factory()->create(['name' => 'English for Kids', 'status' => CourseStatus::Active]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('English for Kids');
        $response->assertSee(__('pages.hero_title'));
        $response->assertSee(__('pages.how_title'));
        $response->assertSee('favicon.svg', false);
    }

    public function test_home_page_renders_in_arabic(): void
    {
        $response = $this->withSession(['locale' => 'ar'])->get(route('home'));

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('بداية');
        $response->assertSee('<title>بداية', false);
    }

    public function test_catalog_lists_only_accepting_courses(): void
    {
        Course::factory()->create(['name' => 'Visible Course', 'status' => CourseStatus::Active]);
        Course::factory()->create(['name' => 'Hidden Draft', 'status' => CourseStatus::Draft]);

        $response = $this->get(route('catalog'));

        $response->assertOk();
        $response->assertSee('Visible Course');
        $response->assertDontSee('Hidden Draft');
    }

    public function test_course_details_show_groups_teachers_and_schedules(): void
    {
        $course = Course::factory()->create([
            'name' => 'Bidaya Collection',
            'description' => 'All subjects in one collection.',
            'status' => CourseStatus::Active,
        ]);
        $group = Group::factory()->create(['course_id' => $course->id, 'name' => 'Arabic Group']);
        GroupSchedule::factory()->create([
            'group_id' => $group->id,
            'day_of_week' => DayOfWeek::Sunday,
        ]);

        $response = $this->get(route('catalog.show', $course));

        $response->assertOk();
        $response->assertSee('Bidaya Collection');
        $response->assertSee('All subjects in one collection.');
        $response->assertSee('Arabic Group');
        $response->assertSee($group->teacher->user->name);
        $response->assertSee(__('pages.details_ask'));
    }

    public function test_draft_course_details_return_not_found(): void
    {
        $course = Course::factory()->create(['status' => CourseStatus::Draft]);

        $this->get(route('catalog.show', $course))->assertNotFound();
    }

    public function test_catalog_renders_in_arabic(): void
    {
        Course::factory()->create(['name' => 'Visible Course', 'status' => CourseStatus::Active]);

        $response = $this->withSession(['locale' => 'ar'])->get(route('catalog'));

        $response->assertOk();
        $response->assertSee(__('pages.catalog_title'));
    }

    public function test_about_and_contact_pages_render(): void
    {
        $this->get(route('about'))->assertOk()->assertSee(__('pages.about_title'));

        $contact = $this->get(route('contact'));
        $contact->assertOk()->assertSee(__('pages.contact_title'));
        $contact->assertSee(__('pages.map_title'));
        $contact->assertSee('google.com/maps', false);
        $contact->assertSee(__('pages.faq_title'));
        $contact->assertSee(__('pages.faq_q1'));
    }

    public function test_contact_form_stores_a_message(): void
    {
        $response = $this->post(route('contact.send'), [
            'name' => 'Interested Parent',
            'phone' => '01001234567',
            'message' => 'What are the fees for English?',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('contact_messages', [
            'phone' => '01001234567',
        ]);
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_contact_form_validates_input(): void
    {
        $response = $this->post(route('contact.send'), []);

        $response->assertSessionHasErrors(['name', 'phone', 'message']);
        $this->assertSame(0, ContactMessage::count());
    }
}

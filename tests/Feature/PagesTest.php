<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Models\ContactMessage;
use App\Models\Course;
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

    public function test_about_and_contact_pages_render(): void
    {
        $this->get(route('about'))->assertOk()->assertSee(__('pages.about_title'));
        $this->get(route('contact'))->assertOk()->assertSee(__('pages.contact_title'));
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

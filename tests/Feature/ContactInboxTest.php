<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_emails_the_centre(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), [
            'name' => 'Interested Parent',
            'phone' => '01001234567',
            'message' => 'What are the fees?',
        ])->assertRedirect(route('contact'));

        $message = ContactMessage::firstOrFail();

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) use ($message): bool {
            return $mail->contactMessage->is($message)
                && $mail->hasTo(config('services.bidaya.contact_email'));
        });
    }

    public function test_contact_email_content_contains_the_message(): void
    {
        $message = ContactMessage::factory()->create([
            'name' => 'Interested Parent',
            'message' => 'What are the fees?',
        ]);

        $mail = new ContactMessageReceived($message);

        $mail->assertSeeInHtml('Interested Parent');
        $mail->assertSeeInHtml('What are the fees?');
    }

    public function test_managers_can_browse_and_delete_messages(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $message = ContactMessage::factory()->create(['name' => 'Interested Parent']);

        $this->actingAs($manager)->get(route('contact-messages.index'))
            ->assertOk()
            ->assertSee('Interested Parent');

        $this->actingAs($manager)->get(route('contact-messages.show', $message))->assertOk();

        $this->actingAs($manager)->delete(route('contact-messages.destroy', $message))
            ->assertRedirect(route('contact-messages.index'));

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_teachers_cannot_access_the_inbox(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('contact-messages.index'))->assertForbidden();
    }
}

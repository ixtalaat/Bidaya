<?php

namespace Tests\Feature;

use App\Enums\GalleryType;
use App\Enums\Role;
use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_shows_only_published_items(): void
    {
        GalleryItem::factory()->create(['title' => 'Sunny Classroom', 'published' => true]);
        GalleryItem::factory()->create(['title' => 'Hidden Draft', 'published' => false]);

        $response = $this->get(route('gallery'));

        $response->assertOk();
        $response->assertSee('Sunny Classroom');
        $response->assertDontSee('Hidden Draft');
    }

    public function test_gallery_filters_by_type(): void
    {
        GalleryItem::factory()->create(['title' => 'Photo Day', 'type' => GalleryType::Photo, 'published' => true]);
        GalleryItem::factory()->create([
            'title' => 'Song Video',
            'type' => GalleryType::Video,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published' => true,
        ]);

        $photos = $this->get(route('gallery', ['type' => 'photo']));
        $photos->assertOk();
        $photos->assertSee('Photo Day');
        $photos->assertDontSee('Song Video');

        $videos = $this->get(route('gallery', ['type' => 'video']));
        $videos->assertOk();
        $videos->assertSee('Song Video');
        $videos->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_teachers_cannot_manage_gallery(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('galleries.index'))->assertForbidden();
        $this->actingAs($teacher)->post(route('galleries.store'), [])->assertForbidden();
    }

    public function test_managers_can_upload_a_photo(): void
    {
        Storage::fake('public');
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('galleries.store'), [
            'type' => GalleryType::Photo->value,
            'title' => 'Art Class',
            'photo' => UploadedFile::fake()->image('art.jpg'),
        ]);

        $item = GalleryItem::where('title', 'Art Class')->firstOrFail();
        $response->assertRedirect(route('galleries.show', $item));
        Storage::disk('public')->assertExists($item->photo_path);
    }

    public function test_video_links_must_be_youtube(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('galleries.store'), [
            'type' => GalleryType::Video->value,
            'title' => 'Bad Video',
            'video_url' => 'https://example.com/video.mp4',
        ]);

        $response->assertSessionHasErrors('video_url');
        $this->assertDatabaseMissing('gallery_items', ['title' => 'Bad Video']);
    }

    public function test_photo_without_upload_is_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('galleries.store'), [
            'type' => GalleryType::Photo->value,
            'title' => 'No Photo',
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertDatabaseMissing('gallery_items', ['title' => 'No Photo']);
    }

    public function test_updating_replaces_the_photo_and_deletes_the_old_one(): void
    {
        Storage::fake('public');
        $manager = User::factory()->withRole(Role::Manager)->create();
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('gallery-photos', 'public');
        $item = GalleryItem::factory()->create([
            'type' => GalleryType::Photo,
            'photo_path' => $oldPath,
        ]);

        $this->actingAs($manager)->put(route('galleries.update', $item), [
            'type' => GalleryType::Photo->value,
            'title' => $item->title,
            'photo' => UploadedFile::fake()->image('new.jpg'),
        ])->assertRedirect(route('galleries.show', $item));

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($item->fresh()->photo_path);
    }

    public function test_deleting_removes_the_photo_file(): void
    {
        Storage::fake('public');
        $manager = User::factory()->withRole(Role::Manager)->create();
        $path = UploadedFile::fake()->image('gone.jpg')->store('gallery-photos', 'public');
        $item = GalleryItem::factory()->create(['type' => GalleryType::Photo, 'photo_path' => $path]);

        $this->actingAs($manager)->delete(route('galleries.destroy', $item))
            ->assertRedirect(route('galleries.index'));

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('gallery_items', ['id' => $item->id]);
    }
}

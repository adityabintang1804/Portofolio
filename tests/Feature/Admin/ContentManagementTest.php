<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_cannot_access_cms_routes(): void
    {
        foreach (['/admin', '/admin/profile', '/admin/projects', '/admin/skills', '/admin/messages'] as $uri) {
            $this->get($uri)->assertRedirect('/admin/login');
        }
    }

    public function test_admin_can_render_dashboard_and_content_pages(): void
    {
        $admin = User::query()->where('email', 'admin@aditya.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk()->assertInertia(fn ($page) => $page->component('Admin/Dashboard')->has('metrics'));
        $this->actingAs($admin)->get('/admin/projects')->assertOk();
        $this->actingAs($admin)->get('/admin/skills')->assertOk();
    }

    public function test_admin_can_create_project_with_technologies(): void
    {
        $admin = User::query()->firstOrFail();
        $category = ProjectCategory::query()->firstOrFail();
        $technologies = Technology::query()->limit(2)->pluck('id')->all();

        $response = $this->actingAs($admin)->post('/admin/projects', [
            'project_category_id' => $category->id,
            'title' => 'Project Testing CMS',
            'slug' => 'project-testing-cms',
            'short_description' => 'Proyek untuk memastikan alur CMS bekerja dengan data relasional.',
            'overview' => 'Overview proyek testing.',
            'status' => 'published',
            'is_featured' => true,
            'display_order' => 20,
            'technology_ids' => $technologies,
        ]);

        $project = Project::query()->where('slug', 'project-testing-cms')->firstOrFail();
        $response->assertRedirect(route('admin.projects.edit', $project));
        $this->assertNotNull($project->published_at);
        $this->assertSame(2, $project->technologies()->count());
    }

    public function test_admin_can_update_profile_and_simple_resource(): void
    {
        $admin = User::query()->firstOrFail();
        $profile = Profile::query()->firstOrFail();
        $payload = $profile->only(['name', 'headline', 'hero_badge', 'hero_description', 'about_description', 'location', 'email', 'phone', 'linkedin_url', 'github_url', 'availability_status', 'availability_text']);
        $payload['headline'] = 'Headline diperbarui melalui CMS';

        $this->actingAs($admin)->put('/admin/profile', $payload)->assertRedirect();
        $this->assertDatabaseHas('profiles', ['headline' => 'Headline diperbarui melalui CMS']);

        $this->actingAs($admin)->post('/admin/technologies', [
            'name' => 'Alpine.js', 'slug' => 'alpine-js', 'icon_key' => 'alpinedotjs',
            'brand_color' => '#77C1D2', 'display_order' => 30, 'is_active' => true,
        ])->assertRedirect(route('admin.technologies.index'));
        $this->assertDatabaseHas('technologies', ['slug' => 'alpine-js']);
    }

    public function test_opening_message_marks_it_read_and_status_can_change(): void
    {
        $admin = User::query()->firstOrFail();
        $message = ContactMessage::factory()->create();

        $this->actingAs($admin)->get(route('admin.messages.show', $message))->assertOk();
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'read']);

        $this->actingAs($admin)->patch(route('admin.messages.status', $message), ['status' => 'archived'])->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'archived']);
    }
}

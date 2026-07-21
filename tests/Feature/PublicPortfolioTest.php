<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Technology;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicPortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render_from_seeded_database_content(): void
    {
        Profile::query()->firstOrFail()->update(['headline' => 'Headline khusus dari database']);

        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('profile.headline', 'Headline khusus dari database')
            ->has('featuredProjects', 4));
        $this->get('/about')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Public/About')->has('educations', 1));
        $this->get('/experience')->assertOk();
        $this->get('/certificates')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/cv')->assertOk();
    }

    public function test_only_published_projects_are_visible_and_draft_detail_is_404(): void
    {
        $published = Project::query()->published()->firstOrFail();
        $draft = Project::query()->where('status', 'draft')->firstOrFail();

        $this->get('/projects')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Projects/Index')
            ->has('projects.data', 5));
        $this->get(route('projects.show', $published))->assertOk();
        $this->get(route('projects.show', $draft))->assertNotFound();
    }

    public function test_project_filters_use_category_and_technology_relations(): void
    {
        $technology = Technology::query()->whereHas('projects', fn ($query) => $query->published())->firstOrFail();

        $this->get('/projects?technology='.$technology->slug)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.technology', $technology->slug)
                ->has('projects.data'));
    }

    public function test_contact_form_validates_and_stores_message(): void
    {
        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        $payload = [
            'name' => 'Recruiter Example', 'email' => 'recruiter@example.com',
            'organization' => 'Example Company', 'subject' => 'Kesempatan magang',
            'message' => 'Kami ingin berdiskusi mengenai kesempatan magang untuk Aditya.', 'website' => '',
        ];
        $this->post('/contact', $payload)->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'recruiter@example.com', 'status' => 'unread']);
    }

    public function test_contact_honeypot_rejects_spam(): void
    {
        $this->post('/contact', [
            'name' => 'Spam', 'email' => 'spam@example.com', 'subject' => 'Spam message',
            'message' => 'Pesan spam dengan honeypot terisi.', 'website' => 'https://spam.example.com',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::query()->count());
    }

    public function test_cv_download_has_clear_fallback_when_file_is_missing(): void
    {
        $this->from('/cv')->get('/cv/download')->assertRedirect('/cv')->assertSessionHas('error');
    }
}

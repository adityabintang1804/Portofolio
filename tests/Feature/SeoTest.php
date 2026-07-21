<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_public_pages_and_only_published_projects(): void
    {
        $published = Project::factory()->create(['status' => 'published', 'published_at' => now()]);
        $draft = Project::factory()->create(['status' => 'draft']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('projects.show', $published), false)
            ->assertDontSee(route('projects.show', $draft), false);
    }

    public function test_robots_allows_public_pages_and_blocks_admin_crawling(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Allow: /')
            ->assertSee('Disallow: /admin')
            ->assertSee(route('sitemap'));
    }

    public function test_public_html_contains_server_rendered_social_metadata(): void
    {
        Profile::factory()->create();
        SiteSetting::query()->create(['key' => 'default_meta_title', 'value' => 'Portfolio SEO', 'type' => 'string', 'group' => 'seo']);
        SiteSetting::query()->create(['key' => 'default_meta_description', 'value' => 'Deskripsi untuk mesin pencari.', 'type' => 'string', 'group' => 'seo']);

        $this->get('/about')
            ->assertOk()
            ->assertSee('<meta name="description" content="Deskripsi untuk mesin pencari.">', false)
            ->assertSee('<meta property="og:title" content="Portfolio SEO">', false)
            ->assertSee('<link rel="canonical" href="'.url('/about').'">', false);
    }
}

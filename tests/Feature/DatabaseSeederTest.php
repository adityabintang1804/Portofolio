<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\ChatbotFaq;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Technology;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_editable_portfolio_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@aditya.test']);
        $this->assertSame(1, Profile::query()->count());
        $this->assertGreaterThanOrEqual(10, SiteSetting::query()->count());
        $this->assertSame(6, ProjectCategory::query()->count());
        $this->assertGreaterThanOrEqual(14, Technology::query()->count());
        $this->assertSame(6, Project::query()->count());
        $this->assertGreaterThanOrEqual(14, Skill::query()->count());
        $this->assertSame(2, Experience::query()->count());
        $this->assertSame(1, Education::query()->count());
        $this->assertSame(3, Certificate::query()->count());
        $this->assertSame(5, ChatbotFaq::query()->count());
        $this->assertTrue(User::query()->where('email', 'admin@aditya.test')->exists());
        $this->assertTrue(Storage::disk('public')->exists(SiteSetting::query()->where('key', 'default_og_image')->value('value')));
    }

    public function test_projects_have_relations_and_drafts_are_not_published(): void
    {
        $this->seed(DatabaseSeeder::class);

        $featured = Project::query()->where('is_featured', true)->firstOrFail();
        $this->assertNotNull($featured->category);
        $this->assertTrue($featured->technologies()->exists());
        $this->assertTrue($featured->images()->exists());
        $this->assertSame(5, Project::query()->published()->count());
        $this->assertFalse(Project::query()->published()->where('status', 'draft')->exists());
    }
}

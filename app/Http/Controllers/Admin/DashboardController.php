<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\ChatbotFaq;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'projects' => Project::query()->count(),
                'draftProjects' => Project::query()->where('status', 'draft')->count(),
                'skills' => Skill::query()->count(),
                'experiences' => Experience::query()->count(),
                'certificates' => Certificate::query()->count(),
                'messages' => ContactMessage::query()->count(),
                'unreadMessages' => ContactMessage::query()->where('status', 'unread')->count(),
            ],
            'chatbotEnabled' => SiteSetting::query()->where('key', 'chatbot_enabled')->value('value') === 'true',
            'activeFaqs' => ChatbotFaq::query()->where('is_active', true)->count(),
            'latestProjects' => Project::query()->latest()->limit(5)->get(['id', 'title', 'slug', 'status', 'updated_at']),
            'latestMessages' => ContactMessage::query()->latest()->limit(5)->get(['id', 'name', 'subject', 'status', 'created_at']),
        ]);
    }
}

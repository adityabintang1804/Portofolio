<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SiteSetting;
use App\Models\SkillCategory;
use App\Models\Technology;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Public/Home', [
            'profile' => Profile::query()->firstOrFail(),
            'settings' => $this->settings(),
            'technologies' => Technology::query()->where('is_active', true)->orderBy('display_order')->get(),
            'featuredProjects' => Project::query()->published()->where('is_featured', true)->with(['category:id,name,slug', 'technologies:id,name,slug,icon_key'])->orderBy('display_order')->limit(5)->get(),
            'skillCategories' => SkillCategory::query()->where('is_active', true)->with(['skills' => fn ($query) => $query->where('is_active', true)])->orderBy('display_order')->get(),
            'experiences' => Experience::query()->where('is_active', true)->orderBy('display_order')->limit(3)->get(),
            'certificates' => Certificate::query()->where('is_active', true)->where('is_featured', true)->with('category:id,name')->orderBy('display_order')->limit(3)->get(),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Public/About', [
            'profile' => Profile::query()->firstOrFail(),
            'educations' => Education::query()->where('is_active', true)->orderBy('display_order')->get(),
            'skillCategories' => SkillCategory::query()->where('is_active', true)->with(['skills' => fn ($query) => $query->where('is_active', true)])->orderBy('display_order')->get(),
            'stats' => [
                'projects' => Project::query()->published()->count(),
                'technologies' => Technology::query()->where('is_active', true)->count(),
                'certificates' => Certificate::query()->where('is_active', true)->count(),
            ],
        ]);
    }

    public function projects(Request $request): Response
    {
        $projects = Project::query()->published()->with(['category:id,name,slug', 'technologies:id,name,slug,icon_key'])
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(fn ($query) => $query->where('title', 'like', $search)->orWhere('short_description', 'like', $search));
            })
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($query) => $query->where('slug', $request->string('category'))))
            ->when($request->filled('technology'), fn ($query) => $query->whereHas('technologies', fn ($query) => $query->where('slug', $request->string('technology'))))
            ->orderByDesc('is_featured')->orderBy('display_order')->paginate(9)->withQueryString();

        return Inertia::render('Public/Projects/Index', [
            'projects' => $projects,
            'categories' => ProjectCategory::query()->where('is_active', true)->orderBy('display_order')->get(['name', 'slug']),
            'technologies' => Technology::query()->where('is_active', true)->whereHas('projects', fn ($query) => $query->published())->orderBy('display_order')->get(['name', 'slug']),
            'filters' => $request->only(['search', 'category', 'technology']),
        ]);
    }

    public function project(Project $project): Response
    {
        abort_unless(Project::query()->published()->whereKey($project->getKey())->exists(), 404);
        $project->load(['category:id,name,slug', 'technologies:id,name,slug,icon_key', 'images']);

        return Inertia::render('Public/Projects/Show', [
            'project' => $project,
            'relatedProjects' => Project::query()->published()->where('project_category_id', $project->project_category_id)->whereKeyNot($project->id)->with('category:id,name')->limit(3)->get(),
        ]);
    }

    public function experience(): Response
    {
        return Inertia::render('Public/Experience', [
            'experiences' => Experience::query()->where('is_active', true)->orderBy('display_order')->get(),
            'educations' => Education::query()->where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function certificates(Request $request): Response
    {
        $certificates = Certificate::query()->where('is_active', true)->with('category:id,name,slug')
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($query) => $query->where('slug', $request->string('category'))))
            ->when($request->filled('issuer'), fn ($query) => $query->where('issuer', $request->string('issuer')))
            ->orderByDesc('is_featured')->orderBy('display_order')->paginate(12)->withQueryString();

        return Inertia::render('Public/Certificates', [
            'certificates' => $certificates,
            'categories' => CertificateCategory::query()->where('is_active', true)->orderBy('display_order')->get(['name', 'slug']),
            'issuers' => Certificate::query()->where('is_active', true)->distinct()->orderBy('issuer')->pluck('issuer'),
            'filters' => $request->only(['category', 'issuer']),
        ]);
    }

    private function settings(): array
    {
        return SiteSetting::query()->pluck('value', 'key')->all();
    }
}

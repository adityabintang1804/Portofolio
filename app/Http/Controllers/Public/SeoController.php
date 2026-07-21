<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('about'), 'priority' => '0.8'],
            ['loc' => route('projects.index'), 'priority' => '0.9'],
            ['loc' => route('experience'), 'priority' => '0.7'],
            ['loc' => route('certificates'), 'priority' => '0.7'],
            ['loc' => route('contact.create'), 'priority' => '0.6'],
            ['loc' => route('cv.show'), 'priority' => '0.6'],
        ])->merge(
            Project::query()->published()->get(['slug', 'updated_at'])->map(fn (Project $project) => [
                'loc' => route('projects.show', $project),
                'lastmod' => $project->updated_at->toAtomString(),
                'priority' => '0.8',
            ]),
        );

        return response()->view('sitemap', ['urls' => $urls], 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain']);
    }
}

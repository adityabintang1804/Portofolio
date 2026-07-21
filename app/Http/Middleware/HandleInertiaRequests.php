<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'site' => fn () => Schema::hasTable('site_settings') ? [
                'settings' => SiteSetting::query()->pluck('value', 'key'),
                'profile' => Profile::query()->first()?->only([
                    'name', 'email', 'location', 'linkedin_url', 'github_url',
                    'availability_status', 'availability_text', 'cv_file',
                ]),
            ] : ['settings' => [], 'profile' => null],
        ];
    }
}

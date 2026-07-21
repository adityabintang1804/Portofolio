<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Public/Cv', ['profile' => Profile::query()->firstOrFail()]);
    }

    public function download(): StreamedResponse|RedirectResponse
    {
        $profile = Profile::query()->firstOrFail();
        if (! $profile->cv_file || ! Storage::disk('public')->exists($profile->cv_file)) {
            return back()->with('error', 'CV belum tersedia. Silakan hubungi Aditya untuk informasi terbaru.');
        }

        return Storage::disk('public')->download($profile->cv_file, 'CV-'.$profile->name.'.pdf');
    }
}

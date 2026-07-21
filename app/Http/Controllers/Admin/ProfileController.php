<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Profile/Edit', ['profile' => Profile::query()->firstOrFail()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $profile = Profile::query()->firstOrFail();
        $data = $request->safe()->except(['profile_image', 'cv_file']);

        if ($request->hasFile('profile_image')) {
            $this->deleteManagedFile($profile->profile_image);
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if ($request->hasFile('cv_file')) {
            $this->deleteManagedFile($profile->cv_file);
            $data['cv_file'] = $request->file('cv_file')->store('cv', 'public');
        }

        $profile->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    private function deleteManagedFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'placeholders/')) {
            Storage::disk('public')->delete($path);
        }
    }
}

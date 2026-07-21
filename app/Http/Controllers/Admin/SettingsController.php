<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'groups' => SiteSetting::query()->orderBy('group')->orderBy('id')->get()->groupBy('group'),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $settings = $request->validated('settings');
        $editable = SiteSetting::query()->whereIn('key', array_keys($settings))->get()->keyBy('key');

        foreach ($settings as $key => $value) {
            $setting = $editable->get($key);
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        return back()->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}

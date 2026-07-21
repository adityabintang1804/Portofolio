<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Public/Contact', ['profile' => Profile::query()->firstOrFail()]);
    }

    public function store(ContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::query()->create($request->safe()->except('website'));

        return back()->with('success', 'Pesan berhasil dikirim. Terima kasih telah menghubungi saya.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $messages = ContactMessage::query()
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(fn ($query) => $query->where('name', 'like', $search)->orWhere('email', 'like', $search)->orWhere('subject', 'like', $search));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(ContactMessage $contactMessage): Response
    {
        if ($contactMessage->status === 'unread') {
            $contactMessage->update(['status' => 'read', 'read_at' => now()]);
        }

        return Inertia::render('Admin/Messages/Show', ['message' => $contactMessage->fresh()]);
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:unread,read,archived']]);
        $contactMessage->update([
            'status' => $data['status'],
            'read_at' => $data['status'] === 'unread' ? null : ($contactMessage->read_at ?? now()),
        ]);

        return back()->with('success', 'Status pesan berhasil diperbarui.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return to_route('admin.messages.index')->with('success', 'Pesan berhasil dihapus.');
    }
}

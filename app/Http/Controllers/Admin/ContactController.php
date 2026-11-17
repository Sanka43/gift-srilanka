<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactReplyRequest;
use App\Jobs\SendContactReplyMail;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $query = Contact::with('user')->latest();

        if ($request->filled('status')) {
            if ($request->status === 'replied') {
                $query->whereNotNull('replied_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('replied_at');
            }
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('subject', 'like', "%{$term}%")
                    ->orWhere('message', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        $contacts = $query->paginate(15)->withQueryString();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function show(Contact $contact): View
    {
        $contact->load('user');
        if (! $contact->read_at) {
            $contact->update(['read_at' => now()]);
        }
        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(ContactReplyRequest $request, Contact $contact): RedirectResponse
    {
        $replyBody = $request->validated('reply');
        $contact->update([
            'admin_reply' => $replyBody,
            'replied_at' => now(),
        ]);

        SendContactReplyMail::dispatch($contact, $replyBody);

        return redirect()->route('admin.contacts.show', $contact)->with('success', 'Reply sent by email and saved.');
    }
}

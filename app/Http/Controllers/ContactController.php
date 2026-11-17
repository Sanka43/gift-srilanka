<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function store(ContactStoreRequest $request): RedirectResponse
    {
        Contact::create([
            'user_id' => $request->user()->id,
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
        ]);

        return redirect()->route('page.contact')->with('success', 'Your message has been sent. We will get back to you soon.');
    }
}

@extends('layouts.admin')

@section('title', 'Contact #' . $contact->id)

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.contacts.index') }}" class="text-sm text-stone-500 hover:text-primary-600 mb-2 inline-block">← Back to contacts</a>
            <h1 class="font-display text-2xl font-bold text-stone-800">Contact #{{ $contact->id }}</h1>
            <p class="text-sm text-stone-500">{{ $contact->created_at->format('M d, Y H:i') }}</p>
        </div>
        @if($contact->replied_at)
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800">Replied {{ $contact->replied_at->format('M d, Y') }}</span>
        @else
            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800">Pending reply</span>
        @endif
    </div>

    <div class="grid gap-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-6">
            <h2 class="font-semibold text-stone-800 mb-3">From</h2>
            <p class="font-medium text-stone-800">{{ $contact->user->name }}</p>
            <p class="text-stone-600">{{ $contact->user->email }}</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6">
            <h2 class="font-semibold text-stone-800 mb-2">Subject</h2>
            <p class="text-stone-700">{{ $contact->subject }}</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6">
            <h2 class="font-semibold text-stone-800 mb-2">Message</h2>
            <div class="text-stone-700 whitespace-pre-wrap">{{ $contact->message }}</div>
        </div>

        @if($contact->admin_reply)
            <div class="rounded-2xl border border-stone-200 bg-stone-50 p-6">
                <h2 class="font-semibold text-stone-800 mb-2">Your reply (sent {{ $contact->replied_at->format('M d, Y H:i') }})</h2>
                <div class="text-stone-700 whitespace-pre-wrap">{{ $contact->admin_reply }}</div>
            </div>
        @endif

        @if(!$contact->replied_at)
            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <h2 class="font-semibold text-stone-800 mb-3">Send reply by email</h2>
                <p class="text-sm text-stone-500 mb-4">Your reply will be sent to {{ $contact->user->email }} and saved here.</p>
                <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST">
                    @csrf
                    <textarea name="reply" id="reply" rows="6" required
                              class="w-full rounded-xl border border-stone-300 px-4 py-3 text-stone-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('reply') border-danger-500 @enderror"
                              placeholder="Type your reply...">{{ old('reply') }}</textarea>
                    @error('reply')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-4 inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Send reply by email
                    </button>
                </form>
            </div>
        @else
            <p class="text-stone-500 text-sm">You already replied to this message. To send another email, the customer would need to submit a new contact form.</p>
        @endif
    </div>
</div>
@endsection

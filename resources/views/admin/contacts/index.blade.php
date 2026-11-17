@extends('layouts.admin')

@section('title', 'Contact messages')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Contact messages</h1>
        <p class="mt-1 text-sm text-stone-500">Messages from registered users. Read and reply by email.</p>
    </div>

    <div class="flex flex-wrap gap-4">
        <form action="{{ route('admin.contacts.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subject, message, user..."
                   class="rounded-xl border border-stone-300 px-3 py-2 text-sm w-64 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            <select name="status" class="rounded-xl border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending reply</option>
                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
            </select>
            <button type="submit" class="rounded-xl bg-stone-100 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-200">Filter</button>
        </form>
    </div>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-stone-50/50 transition-colors {{ !$contact->read_at ? 'bg-primary-50/30' : '' }}">
                            <td class="px-6 py-4 text-sm">
                                <span class="font-medium text-stone-800">{{ $contact->user->name }}</span>
                                <br><span class="text-stone-500">{{ $contact->user->email }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-700 max-w-xs truncate">{{ $contact->subject }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-500">{{ $contact->created_at->format('M d, Y H:i') }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($contact->replied_at)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Replied</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Pending</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View & reply</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">No contact messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-6 py-3 bg-stone-50/30">{{ $contacts->links() }}</div>
    </div>
</div>
@endsection

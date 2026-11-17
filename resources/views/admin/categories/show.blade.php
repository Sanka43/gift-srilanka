@extends('layouts.admin')

@section('title', 'Category: ' . $category->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-stone-500 hover:text-stone-700">← Back to Dashboard</a>
    <span class="mx-2">|</span>
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-stone-500 hover:text-stone-700">Categories</a>
</div>

<div class="bg-white rounded-xl border border-stone-200 p-6 max-w-lg">
    <h2 class="text-xl font-bold text-stone-800 mb-4">{{ $category->name }}</h2>
    <dl class="space-y-2 text-sm">
        <div><dt class="text-stone-500">Slug</dt><dd class="font-medium">{{ $category->slug }}</dd></div>
        <div><dt class="text-stone-500">Status</dt><dd><span class="px-2 py-1 text-xs rounded-full {{ $category->status ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">{{ $category->status ? 'Active' : 'Inactive' }}</span></dd></div>
    </dl>
    <div class="mt-6 flex gap-3">
        <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">Edit</a>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-stone-300 rounded-lg hover:bg-stone-50 text-sm">Back to list</a>
    </div>
</div>
@endsection

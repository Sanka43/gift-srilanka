@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-stone-500 hover:text-stone-700">← Back to Dashboard</a>
</div>

<div class="max-w-lg">
    <h2 class="text-xl font-bold text-stone-800 mb-6">Add Category</h2>
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label for="parent_id" class="block text-sm font-medium text-stone-700 mb-1">Parent category</label>
            <select id="parent_id" name="parent_id" class="admin-select w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none">
                <option value="">— None (main category) —</option>
                @foreach($mainCategories as $main)
                    <option value="{{ $main->id }}" {{ old('parent_id') == $main->id ? 'selected' : '' }}>{{ $main->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-stone-500 mt-1">Leave as "None" to create a main category. Select a main category to create a subcategory.</p>
            @error('parent_id')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-stone-700 mb-1">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-lg border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="slug" class="block text-sm font-medium text-stone-700 mb-1">Slug (optional, auto-generated from name)</label>
            <input id="slug" type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-lg border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Leave blank to auto-generate">
            @error('slug')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-stone-700 mb-1">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="1" {{ old('status', true) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !old('status', true) ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="image" class="block text-sm font-medium text-stone-700 mb-1">Category Image</label>
            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/jpg"
                class="w-full rounded-lg border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            @error('image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-stone-500 mt-1">Optional. JPG, PNG, max 2MB.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Create</button>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-stone-300 rounded-lg hover:bg-stone-50">Cancel</a>
        </div>
    </form>
</div>
@endsection

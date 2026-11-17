@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-300">← Back to Dashboard</a>
</div>

<div class="flex flex-col max-md:flex-col-reverse gap-4 max-md:gap-3 justify-between max-md:items-stretch items-center mb-6">
    <h2 class="text-xl max-md:text-lg font-bold text-stone-800 dark:text-stone-100">Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-4 py-2 max-md:min-h-[2.75rem] bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">Add Category</a>
</div>

<div class="admin-card bg-white dark:bg-stone-800 rounded-xl border border-stone-200 dark:border-stone-600 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-600">
        <thead class="bg-stone-50 dark:bg-stone-800/80">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Products</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 dark:divide-stone-700 bg-white dark:bg-stone-800/40">
            @forelse($categories as $category)
                <tr class="dark:border-stone-700">
                    <td class="px-6 py-4 w-20">
                        @if($category->image_path)
                            <div class="w-12 h-12 rounded border border-stone-200 dark:border-stone-600 overflow-hidden flex-shrink-0 bg-stone-100 dark:bg-stone-700">
                                <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="w-full h-full object-cover" width="48" height="48">
                            </div>
                        @else
                            <span class="text-stone-400 dark:text-stone-500 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-stone-800 dark:text-stone-200">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-stone-500 dark:text-stone-400">{{ $category->slug }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $category->status ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200' : 'bg-stone-100 dark:bg-stone-600 text-stone-600 dark:text-stone-300' }}">
                            {{ $category->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-600 dark:text-stone-300">{{ $category->products_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-stone-500 dark:text-stone-400">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-6 py-3 max-md:px-4 max-md:py-2 border-t border-stone-200 dark:border-stone-700 bg-stone-50/30 dark:bg-stone-800/60">{{ $categories->links() }}</div>
</div>
@endsection

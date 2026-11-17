@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-4 max-md:gap-3">
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-stone-500 hover:text-stone-700 max-md:py-2">← Products</a>
        <h1 class="font-display text-2xl max-md:text-xl font-bold text-stone-800">Edit Product</h1>
    </div>

    @php
        $currentCategory = $product->category;
        $editMainId = $currentCategory && $currentCategory->parent_id ? $currentCategory->parent_id : $currentCategory?->id;
        $editSubId = $currentCategory && $currentCategory->parent_id ? $currentCategory->id : 0;
    @endphp
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
          @submit="(function() { const el = document.querySelector('.product-category-select'); if (el && typeof Alpine !== 'undefined' && Alpine.$data(el) && !Alpine.$data(el).validateBeforeSubmit()) $event.preventDefault(); })()">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="space-y-6">
                <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-md:p-5 max-md:rounded-xl shadow-sm">
                    <h2 class="font-display text-lg max-md:text-base font-semibold text-stone-800 mb-4">Basic info</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1.5">Title / Name *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition hover:border-stone-300">
                            @error('name')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1.5">Slug (auto)</label>
                            <p class="rounded-xl border border-stone-100 bg-stone-50 py-2.5 px-4 text-sm text-stone-600">{{ $product->slug }}</p>
                        </div>
                        <div>
                            @include('admin.products.partials.category-select', ['selectedMainId' => old('main_category_id', $editMainId), 'selectedSubId' => old('subcategory_id', $editSubId), 'selectedCategoryId' => old('category_id', $product->category_id)])
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1.5">Description</label>
                            <textarea name="description" rows="4" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none resize-none">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-md:p-5 max-md:rounded-xl">
                    <h2 class="font-display text-lg max-md:text-base font-semibold text-stone-800 mb-4">Pricing & stock</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1.5">Price (LKR) *</label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1.5">Discount (LKR)</label>
                            <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1.5">Stock quantity *</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-md:p-5 max-md:rounded-xl">
                    <h2 class="font-display text-lg max-md:text-base font-semibold text-stone-800 mb-4">Filters & options</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1.5">Occasion</label>
                                <select name="occasion" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                                    <option value="">— Any —</option>
                                    @foreach(['Birthday', 'Anniversary', 'Valentine', 'Wedding', 'New Baby', 'Corporate'] as $o)
                                        <option value="{{ $o }}" {{ old('occasion', $product->occasion) === $o ? 'selected' : '' }}>{{ $o }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1.5">Recipient</label>
                                <select name="recipient" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                                    <option value="">— Any —</option>
                                    @foreach(['For Him', 'For Her', 'For Kids', 'For Parents', 'For Friends'] as $r)
                                        <option value="{{ $r }}" {{ old('recipient', $product->recipient) === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1.5">Delivery type</label>
                                <select name="delivery_type" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                                    <option value="">— Any —</option>
                                    <option value="one_to_three_days" {{ old('delivery_type', $product->delivery_type) === 'one_to_three_days' ? 'selected' : '' }}>1-3 days</option>
                                    <option value="three_to_five_days" {{ old('delivery_type', $product->delivery_type) === 'three_to_five_days' ? 'selected' : '' }}>3-5 days</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1.5">Rating (0–5)</label>
                                <input type="number" name="rating" value="{{ old('rating', $product->rating) }}" min="0" max="5" step="0.1" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-6 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }} class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm font-medium text-stone-700">Featured</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="in_stock" value="0">
                                <input type="checkbox" name="in_stock" value="1" {{ old('in_stock', $product->in_stock) ? 'checked' : '' }} class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm font-medium text-stone-700">In stock</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_on_sale" value="0">
                                <input type="checkbox" name="is_on_sale" value="1" {{ old('is_on_sale', $product->is_on_sale) ? 'checked' : '' }} class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm font-medium text-stone-700">On sale</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-md:p-5 max-md:rounded-xl">
                    <h2 class="font-display text-lg max-md:text-base font-semibold text-stone-800 mb-4">Status</h2>
                    <select name="status" class="admin-input w-full rounded-xl border border-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                        <option value="active" {{ old('status', $product->status ? 'active' : 'inactive') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                @if($product->images->isNotEmpty())
                    <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-md:p-5 max-md:rounded-xl">
                        <h2 class="font-display text-lg max-md:text-base font-semibold text-stone-800 mb-4">Current images</h2>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($product->images as $img)
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="" class="h-20 w-20 rounded-xl object-cover border border-stone-100">
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-md:p-5 max-md:rounded-xl">
                    <h2 class="font-display text-lg max-md:text-base font-semibold text-stone-800 mb-4">Add more images</h2>
                    <p class="text-sm text-stone-500 mb-2">Maximum 5 images per product. @if($product->images->count() < 5)You can add {{ 5 - $product->images->count() }} more.@endif</p>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700 file:font-medium">
                    @error('images')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('images.*')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 max-md:flex-col max-md:gap-2 border-t border-stone-200 pt-6 max-md:pt-5">
            <button type="submit" class="rounded-xl bg-primary-500 px-6 py-2.5 max-md:min-h-[2.75rem] max-md:w-full text-sm font-semibold text-white shadow-sm hover:bg-primary-600 transition">Update Product</button>
            <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-stone-200 bg-white px-6 py-2.5 max-md:min-h-[2.75rem] max-md:w-full max-md:text-center text-sm font-medium text-stone-700 hover:bg-stone-50 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection

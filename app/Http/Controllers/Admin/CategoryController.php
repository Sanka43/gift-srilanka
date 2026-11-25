<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CacheService $cache
    ) {}

    public function subcategories(Request $request): JsonResponse
    {
        $parentId = $request->integer('parent_id');
        if ($parentId < 1) {
            return response()->json(['subcategories' => []]);
        }
        $subcategories = Category::where('parent_id', $parentId)->orderBy('name')->get(['id', 'name']);
        return response()->json(['subcategories' => $subcategories]);
    }

    public function index(): View
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $mainCategories = Category::mainCategories()->get();
        return view('admin.categories.create', compact('mainCategories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        unset($data['image']);
        $data['parent_id'] = $request->input('parent_id') ?: null;

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageOptimizer::class)->store($request->file('image'), 'categories', 1000, 82);
        }

        Category::create($data);
        $this->cache->invalidateCategoryRelated();

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function show(Category $category): View
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $mainCategories = Category::mainCategories()->get();
        return view('admin.categories.edit', compact('category', 'mainCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        unset($data['image']);
        $data['parent_id'] = $request->input('parent_id') ?: null;

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = app(\App\Services\ImageOptimizer::class)->store($request->file('image'), 'categories', 1000, 82);
        }

        $category->update($data);
        $this->cache->invalidateCategoryRelated();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with products.');
        }
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete a main category that has subcategories. Move or delete subcategories first.');
        }
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }
        $category->delete();
        $this->cache->invalidateCategoryRelated();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}

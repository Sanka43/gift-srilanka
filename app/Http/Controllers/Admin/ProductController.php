<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->withCount('reviews')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->string('search'));

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->value() === 'active');
            })
            ->when($request->filled('min_rating'), function ($query) use ($request) {
                $query->where('rating', '>=', (float) $request->input('min_rating'));
            })
            ->when($request->boolean('low_stock'), function ($query) {
                $query->where('stock', '<=', 5);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $mainCategories = Category::mainCategories()->get();
        return view('admin.products.create', compact('mainCategories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());
        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load('images', 'category.parent');
        $mainCategories = Category::mainCategories()->get();
        return view('admin.products.edit', compact('product', 'mainCategories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->productService->update($product, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}

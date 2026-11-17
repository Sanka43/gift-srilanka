{{--
  Category selection: Main + optional Subcategory.
  Uses single category_id (hidden). Alpine sets it to subcategory_id if selected, else main_category_id.
  Expects: $mainCategories, optional $selectedMainId, $selectedSubId, $selectedCategoryId (for edit).
--}}
@php
    $subcategoriesUrl = route('admin.categories.subcategories');
    $selectedMainId = $selectedMainId ?? old('main_category_id');
    $selectedSubId = $selectedSubId ?? old('subcategory_id');
    $selectedCategoryId = $selectedCategoryId ?? old('category_id');
    $mainCategoriesJson = $mainCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values();
@endphp
<div class="product-category-select" x-data="productCategorySelect({{ json_encode($subcategoriesUrl) }}, {{ json_encode($mainCategoriesJson) }}, {{ (int) ($selectedMainId ?? 0) }}, {{ (int) ($selectedSubId ?? 0) }}, {{ (int) ($selectedCategoryId ?? 0) }})" x-init="init()" x-ref="categorySelect">
    <input type="hidden" name="category_id" :value="effectiveCategoryId" x-ref="categoryIdInput">

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Main Category *</label>
            <select x-model.number="mainCategoryId"
                    @change="onMainChange()"
                    class="admin-select w-full rounded-xl border border-stone-200 bg-white py-2.5 px-4 text-sm text-stone-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition hover:border-stone-300"
                    :class="{ 'border-danger-400': categoryError }">
                <option value="">— Select main category —</option>
                @foreach($mainCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-stone-500">If subcategory exists, please select one.</p>
            @error('category_id')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
            @enderror
            <p x-show="categoryError" x-cloak class="mt-1 text-sm text-danger-600" x-text="categoryErrorMsg"></p>
        </div>

        <div x-show="showSubcategory"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="overflow-hidden">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Subcategory</label>
            <select x-model.number="subcategoryId"
                    @change="syncEffective()"
                    class="admin-select w-full rounded-xl border border-stone-200 bg-white py-2.5 px-4 text-sm text-stone-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition hover:border-stone-300">
                <option value="">— Select subcategory —</option>
                <template x-for="sub in subcategories" :key="sub.id">
                    <option :value="sub.id" x-text="sub.name"></option>
                </template>
            </select>
        </div>

        <div class="rounded-xl border border-stone-100 bg-stone-50/80 py-2.5 px-4 text-sm">
            <span class="text-stone-500">Category:</span>
            <span class="font-medium text-stone-800 ml-1" x-text="breadcrumbText"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productCategorySelect', (subcategoriesUrl, mainCategories, selectedMainId, selectedSubId, selectedCategoryId) => ({
        subcategoriesUrl,
        mainCategories: mainCategories || [],
        mainCategoryId: selectedMainId || '',
        subcategoryId: selectedSubId || '',
        subcategories: [],
        showSubcategory: false,
        categoryError: false,
        categoryErrorMsg: '',
        get mainCategory() {
            return this.mainCategories.find(c => c.id == this.mainCategoryId) || null;
        },
        get hasSubcategories() {
            return this.subcategories && this.subcategories.length > 0;
        },
        get effectiveCategoryId() {
            if (this.subcategoryId) return this.subcategoryId;
            return this.mainCategoryId || '';
        },
        get breadcrumbText() {
            if (!this.mainCategory) return '—';
            const mainName = this.mainCategory.name;
            if (this.subcategoryId) {
                const sub = this.subcategories.find(s => s.id == this.subcategoryId);
                return sub ? `${mainName} → ${sub.name}` : mainName;
            }
            return mainName;
        },
        init() {
            if (selectedMainId) this.loadSubcategories(selectedMainId);
            this.syncEffective();
        },
        onMainChange() {
            this.subcategoryId = '';
            this.subcategories = [];
            this.showSubcategory = false;
            this.categoryError = false;
            if (this.mainCategoryId) {
                this.loadSubcategories(this.mainCategoryId);
            }
            this.syncEffective();
        },
        async loadSubcategories(parentId) {
            try {
                const res = await fetch(`${this.subcategoriesUrl}?parent_id=${parentId}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.subcategories = data.subcategories || [];
                this.showSubcategory = this.subcategories.length > 0;
            } catch (e) {
                this.subcategories = [];
                this.showSubcategory = false;
            }
        },
        syncEffective() {
            this.$nextTick(() => {
                const input = this.$refs.categoryIdInput;
                if (input) input.value = this.effectiveCategoryId;
            });
        },
        validateBeforeSubmit() {
            if (!this.mainCategoryId) {
                this.categoryError = true;
                this.categoryErrorMsg = 'Please select a main category.';
                return false;
            }
            if (this.hasSubcategories && !this.subcategoryId) {
                this.categoryError = true;
                this.categoryErrorMsg = 'Please select a subcategory.';
                return false;
            }
            this.categoryError = false;
            this.categoryErrorMsg = '';
            return true;
        }
    }));
});
</script>

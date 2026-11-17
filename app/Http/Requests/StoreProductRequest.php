<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'featured' => ['boolean'],
            'occasion' => ['nullable', 'string', 'in:Birthday,Anniversary,Valentine,Wedding,New Baby,Corporate'],
            'recipient' => ['nullable', 'string', 'in:For Him,For Her,For Kids,For Parents,For Friends'],
            'delivery_type' => ['nullable', 'string', 'in:one_to_three_days,three_to_five_days'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'in_stock' => ['boolean'],
            'is_on_sale' => ['boolean'],
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Please upload at least one image.',
            'images.min' => 'Please upload at least one image.',
            'images.max' => 'You may upload at most 5 images per product.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'in_stock' => $this->boolean('in_stock'),
            'is_on_sale' => $this->boolean('is_on_sale'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $categoryId = $this->input('category_id');
            if (! $categoryId) {
                return;
            }
            $category = Category::find($categoryId);
            if ($category && $category->hasSubcategories()) {
                $validator->errors()->add('category_id', 'Please select a subcategory for this main category.');
            }
        });
    }
}

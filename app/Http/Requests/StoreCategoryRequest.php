<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
        if ($this->has('status')) {
            $this->merge(['status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN)]);
        }
        if ($this->input('parent_id') === '' || $this->input('parent_id') === null) {
            $this->merge(['parent_id' => null]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $parentId = $this->input('parent_id');
            if ($parentId && Category::where('id', $parentId)->whereNotNull('parent_id')->exists()) {
                $validator->errors()->add('parent_id', 'Parent must be a main category (not a subcategory).');
            }
        });
    }
}

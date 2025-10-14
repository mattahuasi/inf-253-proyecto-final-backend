<?php

namespace App\Http\Requests;

use App\Rules\SlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.type' => 'in:categories',
            'data.attributes.name' => 'required|min:3|max:45',
            'data.attributes.slug' => [
                'required',
                new SlugRule(),
                // "unique:categories,slug,{$this->route('category')}",
                Rule::unique('categories', 'slug')->ignore($this->route('category')),
                'min:3',
                'max:45'
            ],
            'data.attributes.description' => 'required|min:3|max:180',
            'data.attributes.priority' => 'required|in:0,1,2,3,4,5,6,7,8,9',
        ];
    }

    public function validatedAttributes(): array
    {
        return parent::validated()['data']['attributes'];
    }
}

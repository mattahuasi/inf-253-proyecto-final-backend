<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\SlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'data.type' => 'required|in:menus',
            'data.attributes.name' => 'required|string|min:3|max:90',
            'data.attributes.slug' => [
                'required',
                'string',
                new SlugRule(),
                Rule::unique('menus', 'slug')->ignore($this->route('menu')),
                'min:3',
                'max:45'
            ],
            'data.attributes.description' => 'required|string|min:3|max:225',
            'data.attributes.price' => 'required|numeric|min:0|max:99999999.99',
            // 'data.attributes.photo' => 'nullable|string|max:180',
            'data.attributes.stock' => 'required|integer|min:0|max:100',
            'data.attributes.priority' => 'required|in:H,M,L',
            'data.attributes.enabled' => 'required|boolean',
            'data.relationships.category.data.id' => [
                Rule::requiredIf(!$this->route('menu')),
                'exists:categories,slug'
            ],
            'data.relationships.category.data.type' => [
                Rule::requiredIf(!$this->route('menu')),
                'in:categories'
            ]
            // 'data.attributes.category_id' => 'required|exists:categories,id'
        ];
    }
    public function validatedAttributes(): array
    {
        $data = parent::validated()['data'];
        $attributes = $data['attributes'];
        if (isset($data['relationships'])) {
            $relationships = $data['relationships'];
            $id = $relationships['category']['data']['id'];
            $category = Category::select('id')->whereRouteKey($id)->first();
            $attributes['category_id'] = $category->id;
        }
        return $attributes;
    }
}

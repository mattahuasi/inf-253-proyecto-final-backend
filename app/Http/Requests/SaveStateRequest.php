<?php

namespace App\Http\Requests;

use App\Rules\SlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.type' => 'string|in:states',
            'data.attributes.name' => [
                'required',
                'min:3',
                'max:45',
                Rule::unique('states', 'name')->ignore($this->route('state')),
            ],
            'data.attributes.slug' => [
                'required',
                'min:3',
                'max:45',
                new SlugRule(),
                Rule::unique('states', 'slug')->ignore($this->route('state'))
            ],
            'data.attributes.description' => 'required|min:3|max:180',
            'data.attributes.color' => [
                'required',
                'hex_color',
                Rule::unique('states', 'color')->ignore($this->route('state'))
            ]
        ];
    }

    public function validatedAttributes(): array
    {
        return parent::validated()['data']['attributes'];
    }
}

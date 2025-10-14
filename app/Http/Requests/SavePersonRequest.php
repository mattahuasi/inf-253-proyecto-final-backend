<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePersonRequest extends FormRequest
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
            'data.type' => 'required|in:customers,employees',
            "data.attributes.paternal_surname" => "nullable|string|min:3|max:20|required_without:data.attributes.maternal_surname",
            "data.attributes.maternal_surname" => "nullable|string|min:3|max:20|required_without:data.attributes.paternal_surname",
            'data.attributes.names' => 'required|string|min:3|max:45',
            'data.attributes.gender' => "required|in:M,F",
            'data.attributes.phone' => "nullable|string|min:8|max:15",
            'data.attributes.type' => [
                Rule::requiredIf($this->routeIs('api.employees.store') || $this->routeIs('api.employees.update')),
                'string',
                'in:AD,CO,CA,WA'
            ],
        ];
    }

    public function validatedAttributes(): array
    {
        $data = parent::validated()['data'];
        $attributes = $data['attributes'];
        return $attributes;
    }
}

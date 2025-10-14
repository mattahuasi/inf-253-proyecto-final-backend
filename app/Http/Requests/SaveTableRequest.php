<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTableRequest extends FormRequest
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
            'data.attributes.number' => [
                'required',
                'integer',
                'min:1',
                'max:50',
                Rule::unique('tables', 'number')->ignore($this->route('table')),
            ],
            'data.attributes.status' => 'required|string|in:A,B,W', // Busy B, Available A, Waiting W
            'data.attributes.ability' => 'required|integer|in:4,5,6,7,8',
        ];
    }

    public function validatedAttributes(): array
    {
        return parent::validated()['data']['attributes'];
    }
}

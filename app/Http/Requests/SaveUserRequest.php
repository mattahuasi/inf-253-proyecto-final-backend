<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveUserRequest extends FormRequest
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
        // dd($this->route('user'));
        $rules = [
            'data.type' => 'required|in:users',
            'data.attributes.username' => 'required|string|min:3|max:45',
            'data.attributes.enabled' => 'required|boolean',
            'data.attributes.email' => [
                'required',
                'email',
                'min:3',
                'max:180',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'data.relationships.role.data.type' => 'required|string|in:roles',
            'data.relationships.role.data.id' => 'required|string|exists:roles,id',
        ];
        if (!$this->route('user')) {
            $rules['data.attributes.user_type'] =  'required|string|in:employee,customer';
            $this->addEntityRelationshipRules($rules);
        }

        return $rules;
    }

    protected function addEntityRelationshipRules(array &$rules): void
    {
        $userType = $this->input('data.attributes.user_type');

        if (in_array($userType, ['employee', 'customer'])) {
            $entity = $userType === 'employee' ? 'employee' : 'customer';
            $rules["data.relationships.{$entity}.data.type"] = "required|string|in:{$entity}s";
            $rules["data.relationships.{$entity}.data.id"] = "required|string|exists:{$entity}s,person_id";
        }
    }


    public function validatedAttributes(): array
    {
        $data = parent::validated()['data'];
        $attributes = $data['attributes'];

        if (isset($data['relationships'])) {
            $attributes['role_id'] = $this->getRoleIdFromRouteKey($data['relationships']['role']['data']['id']);
            if (!$this->route('user'))
                $attributes['person_id'] = $this->getEntityIdFromRouteKey($attributes['user_type'], $data['relationships']);
        }

        return $attributes;
    }

    protected function getRoleIdFromRouteKey(string $roleRouteKey): string
    {
        $role = Role::select('id')->find($roleRouteKey);
        return (string) $role->id;
    }

    protected function getEntityIdFromRouteKey(string $userType, array $relationships): string
    {
        $entityRouteKey = $relationships[$userType]['data']['id'];

        $entityModel = $userType === 'customer' ? Customer::class : Employee::class;

        $entity = $entityModel::select('person_id')->find($entityRouteKey);

        return (string) $entity->person_id;
    }
}

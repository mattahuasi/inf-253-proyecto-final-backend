<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class EmployeeResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'paternal_surname' => $this->person->paternal_surname,
            'maternal_surname' => $this->person->maternal_surname,
            'names' => $this->person->names,
            'gender' => $this->person->gender,
            'phone' => $this->person->phone,
            'type' => $this->type,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['user','addresses'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $user = $this->whenLoaded('user');

        if (!($user instanceof MissingValue))
            $data['user'] = UserResource::make($user);

        return array_filter($data);
    }


    public function getIncludes(): array
    {
        $data = [];

        $user = $this->whenLoaded('user');
        if (!($user instanceof MissingValue) && $user->exists)
            $data['user'] = UserResource::make($user);

        return array_filter($data);
    }
}

<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class UserResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'enabled' => $this->enabled,
            'user_type' => $this->person->type
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['role', $this->person->type];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $role = $this->whenLoaded('role');

        if (!($role instanceof MissingValue))
            $data['role'] = RoleResource::make($role);

        return array_filter($data);
    }

    public function getIncludes(): array
    {
        return [
            'role' => RoleResource::make($this->whenLoaded('role')),
        ];
    }
}

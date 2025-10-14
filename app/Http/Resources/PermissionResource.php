<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class PermissionResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['roles'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $roles = $this->whenLoaded('roles');
        if (!($roles instanceof MissingValue)) {
            foreach ($roles as $key => $role) {
                $data['roles'][] = RoleResource::make($role);
            }
        }

        return $data;
    }

    public function getIncludes(): array
    {
        $data = [];
        $roles = $this->whenLoaded('roles');
        if (!($roles instanceof MissingValue)) {
            foreach ($roles as $key => $role) {
                $data[] = RoleResource::make($role);
            }
        }
        return $data;
    }
}

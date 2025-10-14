<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class RoleResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'name' => $this->name
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['permissions'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $permissions = $this->whenLoaded('permissions');
        if (!($permissions instanceof MissingValue)) {
            foreach ($permissions as $key => $permission) {
                $data['permissions'][] = PermissionResource::make($permission);
            }
        }

        return $data;
    }

    public function getIncludes(): array
    {
        $data = [];
        $permissions = $this->whenLoaded('permissions');
        if (!($permissions instanceof MissingValue)) {
            foreach ($permissions as $key => $permission) {
                $data[] = PermissionResource::make($permission);
            }
        }
        return $data;
    }
}

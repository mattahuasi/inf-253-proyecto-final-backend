<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color
        ];
    }

    // public function getRelationshipLinks(): array
    // {
    //     // return ['orders'];
    // }
}

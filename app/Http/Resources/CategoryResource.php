<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class CategoryResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'priority' => $this->priority
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['menus'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $menus = $this->whenLoaded('menus');
        if (!($menus instanceof MissingValue)) {
            foreach ($this->menus as $key => $menu) {
                $data['menus'][] = MenuResource::make($menu);
            }
        }
        return $data;
    }

    public function getIncludes(): array
    {
        $data = [];
        $menus = $this->whenLoaded('menus');
        if (!($menus instanceof MissingValue)) {
            foreach ($menus as $key => $menu) {
                $data[] = MenuResource::make($menu);
            }
        }
        return $data;
    }
    // public function getResourceType()
    // {
    //     return 'categories'; // Define el tipo de recurso
    // }
}

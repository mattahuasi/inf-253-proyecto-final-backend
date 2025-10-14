<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class MenuResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'photo_url' => $this->photo_url,
            // 'photo' => $this->photo,
            'stock' => $this->stock,
            'priority' => $this->priority,
            'enabled' => (bool) $this->enabled,
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['category'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $category = $this->whenLoaded('category');

        if (!($category instanceof MissingValue))
            $data['category'] = CategoryResource::make($category);

        return array_filter($data);
    }

    public function getIncludes(): array
    {
        return [
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}

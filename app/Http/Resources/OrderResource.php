<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class OrderResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            // 'name' => $this->name,
            // 'slug' => $this->slug,
            // 'description' => $this->description,
            // 'price' => $this->price,
            // 'photo_url' => $this->photo_url,
            // 'stock' => $this->stock,
            // 'priority' => $this->priority,
            // 'enable' => (bool) $this->enable,
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['table'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $table = $this->whenLoaded('table');

        if (!($table instanceof MissingValue))
            $data['table'] = TableResource::make($table);

        return array_filter($data);
    }

    public function getIncludes(): array
    {
        return [
            'table' => TableResource::make($this->whenLoaded('table')),
        ];
    }
}

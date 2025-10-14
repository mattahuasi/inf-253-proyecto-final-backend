<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class TableResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'number' => $this->number,
            'status' => $this->status,
            'ability' => $this->ability,
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['orders'];
    }

    public function getRelationshipData(): array
    {
        $data = [];
        $orders = $this->whenLoaded('orders');
        if (!($orders instanceof MissingValue)) {
            foreach ($orders as $key => $order) {
                $data['orders'][] = OrderResource::make($order);
            }
        }
        return $data;
    }

    public function getIncludes(): array
    {
        $data = [];
        $orders = $this->whenLoaded('orders');
        if (!($orders instanceof MissingValue)) {
            foreach ($orders as $key => $order) {
                $data[] = OrderResource::make($order);
            }
        }
        return $data;
    }
}

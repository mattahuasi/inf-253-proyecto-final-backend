<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\BaseJsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    use BaseJsonApiResource;

    public function toResourceAttributes(): array
    {
        return [
            'zona' => $this->zona,
            'street' => $this->street,
            'detail' => $this->detail,
        ];
    }

    public function getLinkSelf(): string
    {
        $route = route('api.' . $this->person->type . 's.' . $this->getResourceType() . '.show', [
            $this->person->type => $this->person->id,
            'address' => $this
        ]);
        return $route;
    }
}

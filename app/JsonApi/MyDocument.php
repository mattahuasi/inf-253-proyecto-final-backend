<?php

namespace App\JsonApi;

use Illuminate\Support\Collection;

class MyDocument extends Collection
{
    public static function type(string $type): MyDocument
    {
        return new self([
            'data' => [
                'type' => $type
            ]
        ]);
    }

    public function id(?string $id): MyDocument
    {
        if ($id)
            $this->items['data']['id'] = (string) $id;

        return $this;
    }

    public function attributes(array $attributes): MyDocument
    {
        unset($attributes['_relationships']);
        $this->items['data']['attributes'] = $attributes;
        return $this;
    }

    public function links(array $links): MyDocument
    {
        $this->items['data']['links'] = $links;
        return $this;
    }

    public function relationshipData(array $relationships): MyDocument
    {
        foreach ($relationships as $key => $relationship) {
            if (is_array($relationship) || $relationship instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($relationship as $item) {
                    if ($item?->getRouteKey()) {
                        $this->items['data']['relationships'][$key]['data'][] = [
                            'type' => $item?->getResourceType(),
                            'id' => (string)$item?->getRouteKey()
                        ];
                    }
                }
            } else {
                if ($relationship?->getRouteKey()) {
                    $this->items['data']['relationships'][$key]['data'] = [
                        'type' => $relationship?->getResourceType(),
                        'id' => (string)$relationship?->getRouteKey()
                    ];
                } else {
                    $this->items['data']['relationships'][$key]['data'] = null;
                }
            }
        }
        return $this;
    }

    public function relationshipLinks(array $relationships): MyDocument
    {
        foreach ($relationships as $key) {
            $this->items['data']['relationships'][$key]['links'] = $this->generateLinks($key);
        }
        return $this;
    }

    private function generateLinks(string $key): array
    {
        $type = $this->items['data']['type'];
        $id = $this->items['data']['id'];
        return [
            'self' => route("api.{$type}.relationships.{$key}.show", $id),
            'related' => route("api.{$type}.{$key}", $id),
        ];
    }
}

<?php

namespace App\JsonApi\Traits;

use App\JsonApi\MyDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait BaseJsonApiResource
{
    abstract function toResourceAttributes(): array;

    public function toArray(Request $request): array
    {
        if (!$this->exists)
            return ['data' => null];

        $document = MyDocument::type($this->getResourceType())
            ->id($this->getRouteKey())
            ->attributes($this->filterAttributes($this->toResourceAttributes()))
            ->relationshipLinks($this->getRelationshipLinks())
            ->relationshipData($this->getRelationshipData())
            ->links([
                'self' => $this->getLinkSelf()
            ]);

        if ($request->filled('include')) {
            $included = collect();
            foreach ($this->getIncludes() as $key => $include) {
                $included->add($include);
            }
            $this->with['included'] = $included->unique();
        }

        return $document->get('data');
    }

    public function getLinkSelf(): string
    {
        return route('api.' . $this->getResourceType() . '.show', $this);
    }

    public function getIncludes(): array
    {
        return [];
    }

    public function getRelationshipLinks(): array
    {
        return [];
    }

    public function getRelationshipData(): array
    {
        return [];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        if ($this->exists) {
            $response->header('Location', $this->getLinkSelf());
        }
    }

    public function filterAttributes(array $attributes)
    {
        return array_filter(
            $attributes,
            function ($value) {
                if (request()->isNotFilled('fields'))
                    return true;

                $fields = (explode(',', request("fields." . $this->getResourceType())));

                if ($value === $this->getRouteKey())
                    return in_array($this->getRouteKeyName(), $fields);

                return $value;
            }
        );
    }

    public static function collection($resources)
    {
        $collection = parent::collection($resources);
        if (request()->filled('include')) {
            $included = collect();
            foreach ($collection as $resource) {
                foreach ($resource->getIncludes() as $include)
                    $included->add($include);
            }
            $collection->with['included'] = $included->unique(function ($item) {
                return $item['type'] . '-' . $item['id'];
            });
        }

        if (request()->has('page.number') || request()->has('page.size')) {
            $collection->with['links'] = ['self' => $resources->path()];
        }
        return $collection;
    }

    public static function identifier($resource): array
    {
        if ($resource->exists)
            return MyDocument::type($resource->getResourceType())
                ->id($resource->getRouteKey())->toArray();
        else
            return ['data' => null];
    }

    public static function identifiers($resources): array
    {
        $data['data'] = [];

        foreach ($resources as $key => $resource)
            $data['data'][] = MyDocument::type($resource->getResourceType())
                ->id($resource->getRouteKey())->get('data');

        return $data;
    }
}

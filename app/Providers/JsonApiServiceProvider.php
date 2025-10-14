<?php

namespace App\Providers;

use App\Models\Employee;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('allowedFilters', function (array $allowedFilterFields) {
            /** @var Builder $this */
            $query = $this;

            foreach (request('filter', []) as $field => $value) {
                $allowed = false;
                $scope = null;
                foreach ($allowedFilterFields as $allowedFilterField) {
                    if (str_contains($allowedFilterField, ':')) {
                        [$relation, $scope] = explode(':', $allowedFilterField);
                        if ($field === $relation) {
                            $allowed = true;
                            break;
                        }
                    } elseif ($field === $allowedFilterField) {
                        $allowed = true;
                        break;
                    }
                }
                if (!$allowed) {
                    $message =  "The filter field '{$field}' is not allowed in the '{$this->getResourceType()}' resource.";
                    throw new BadRequestHttpException($message);
                }

                if ($scope !== null) {
                    if ($this->hasNamedScope($scope))
                        $this->$scope($value);
                    else
                        throw new \App\Exceptions\JsonApi\ServerErrorHttpException("Named scope '{$scope}' is not defined.");
                } elseif (strpos($field, '.') !== false) {
                    $relationParts = explode('.', $field);
                    $relationName = $relationParts[0];
                    $relationField = $relationParts[1];
                    $query->orWhereHas($relationName, function ($query) use ($relationField, $value) {
                        $query->where($relationField, 'like', '%' . $value . '%');
                    });
                } else {
                    $query->orWhere($field, 'like', '%' . $value . '%');
                }
            }
            return $query;
        });

        Builder::macro('allowedSorts', function (array $allowedSortFields) {
            /** @var Builder $this */

            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));

                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    $sortField = ltrim($sortField, '-');

                    if (!in_array($sortField, $allowedSortFields)) {
                        $message = "The sort field '{$sortField}' is not allowed in the '{$this->getResourceType()}' resource.";
                        throw new BadRequestHttpException($message);
                    }

                    if ($this->hasNamedScope("orderBy" . Str::ucfirst($sortField)))
                        $this->{'orderBy' . Str::ucfirst($sortField)}($sortDirection);
                    else
                        $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        });

        Builder::macro('allowedIncludes', function (array $allowedIncludes) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */

            if (request()->isNotFilled('include'))
                return $this;

            $includes = explode(',', request()->input('include'));

            foreach ($includes as $include) {

                if (!in_array($include, $allowedIncludes)) {
                    $message = "The included relationship '{$include}' is not allowed in the '{$this->getResourceType()}' resource.";
                    throw new BadRequestHttpException($message);
                }

                $this->with($include);
            }

            return $this;
        });

        Builder::macro('sparseFieldset', function () {
            /** @var \Illuminate\Database\Query\Builder $this */
            if (request()->isNotFilled('fields'))
                return $this;

            $fields = (explode(',', request("fields." . $this->getResourceType())));
            $routeKey = $this->getModel()->getRouteKeyName();
            if (!in_array($routeKey, $fields))
                $fields[] = $routeKey;

            return $this->addSelect($fields);
        });

        Builder::macro('getResourceType', function () {
            /** @var \Illuminate\Database\Query\Builder $this */
            $resourceType = $this->getModel()->getTable();
            if (property_exists($this->getModel(), 'resourceType'))
                $resourceType = $this->getModel()->resourceType;
            return $resourceType;
        });

        Builder::macro('jsonApiPaginate', function ($perPage = 15, $page = 1) {
            /** @var Builder $this */
            $query = $this;
            if (request()->has('page.number') || request()->has('page.size')) {
                $perPage = request('page.size', $perPage);
                $page = request('page.number', $page);
                $items = $query->paginate($perPage, ['*'], 'page[number]', $page);
                $items->appends(
                    request()->only('sort', 'filter', 'page.size')
                    // [
                    // 'page[size]' => $perPage,
                    // 'filter' => request('filter', []),
                    // 'sort' => request('sort'),
                    // ]
                );
                return $items;
            }
            return $query->get();
        });

        TestResponse::macro(
            'assertJsonApiValidationErrors',
            function ($attribute) {
                /** @var TestResponse $this */

                switch (true) {
                    case Str::of($attribute)->startsWith('data'):
                        $pointer = '/' . str_replace('.', '/', $attribute);
                        break;
                    case Str::of($attribute)->startsWith('relationships'):
                        $pointer = '/data/' . str_replace('.', '/', $attribute) . '/data/id';
                        break;
                    default:
                        $pointer = '/data/attributes/' . $attribute;
                }
                // dump('----------',$pointer,'----------');

                try {
                    $this->assertJsonFragment([
                        'source' => ['pointer' => $pointer]
                    ]);
                } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
                    Assert::fail("Failed to find JSON:API validation error for key: '{$attribute}'" . PHP_EOL . PHP_EOL . $e->getMessage());
                }

                try {
                    $this->assertJsonStructure([
                        'errors' => [
                            ['title', 'detail', 'source' => ['pointer']]
                        ]
                    ]);
                } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
                    Assert::fail("Failed to find a valid JSON:API error response" . PHP_EOL . PHP_EOL . $e->getMessage());
                }


                $this->assertHeader(
                    'content-type',
                    'application/vnd.api+json'
                )->assertStatus(422);
            }
        );

        TestResponse::macro(
            'assertJsonApiResource',
            function (Model $model, array $attributes, $self = null) {
                /** @var TestResponse $this */
                // dd($self);
                $linkSelf = $self ?? route('api.' . $model->getResourceType() . '.show',  $model);
                $location = $linkSelf;

                $data['data'] = [
                    'type' => $model->getResourceType(),
                    'id' => (string)$model->getRouteKey(),
                    'attributes' => $attributes,
                    'links' => [
                        'self' => $linkSelf
                    ]
                ];

                return $this->assertJson($data)->assertHeader('Location', $location);
            }
        );

        TestResponse::macro(
            'assertJsonApiResourceCollection',
            function ($models, array $attributeKeys) {
                /** @var TestResponse $this */

                try {
                    $this->assertJsonStructure([
                        'data' => [
                            '*' => [
                                'attributes' => $attributeKeys,
                                'links' => [
                                    'self'
                                ],
                            ]
                        ]
                    ]);
                } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
                    Assert::fail("La respuesta JSON no tiene la estructura esperada. Asegúrate de que contiene 'data' con 'attributes' válidos." . PHP_EOL . PHP_EOL . "Error: " . $e->getMessage());
                }

                foreach ($models as $model) {
                    try {
                        $this->assertJsonFragment([
                            'type' => $model->getResourceType(),
                            'id' => (string)$model->getRouteKey()
                        ]);
                    } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
                        Assert::fail("La respuesta JSON no contiene el fragmento esperado para el modelo de tipo '" . $model->getResourceType() . "' con ID '" . (string)$model->getRouteKey() . "'. Asegúrate de que la respuesta incluya la información correcta." . PHP_EOL . PHP_EOL . "Error: " . $e->getMessage());
                    }
                }
                return $this;
            }
        );

        TestResponse::macro(
            'assertJsonApiRelationshipLinks',
            function (Model $model, array $relations) {
                /** @var TestResponse $this */
                foreach ($relations as $relation) {
                    $this->assertJson(
                        [
                            'data' => [
                                'relationships' => [
                                    $relation => [
                                        'links' => [
                                            'self' => route("api.{$model->getResourceType()}.relationships.{$relation}.show", $model),
                                            'related' => route("api.{$model->getResourceType()}.{$relation}", $model),
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    );
                }
                return $this;
            }
        );

        TestResponse::macro(
            'assertJsonApiCollectionRelationshipLinks',
            function ($models, array $relations) {
                /** @var TestResponse $this */
                foreach ($models as $model) {
                    foreach ($relations as $relation) {
                        $this->assertJsonStructure([
                            'data' => [
                                '*' => [
                                    'relationships' => [
                                        $relation => ['links' => ['self', 'related'],]
                                    ]
                                ]
                            ]
                        ]);
                        $this->assertJsonFragment([
                            'links' => [
                                'self' => route("api.{$model->getResourceType()}.relationships.{$relation}.show", $model),
                                'related' => route("api.{$model->getResourceType()}.{$relation}", $model),
                            ]
                        ]);
                    }
                }

                return $this;
            }
        );


        // TestResponse::macro(
        //     'assertJsonApiCollectionRelationshipLinks',
        //     function ($models, array $relations) {
        //         /** @var TestResponse $this */
        //         foreach ($models as $model) {
        //             $relationships['relationships'] = [];
        //             foreach ($relations as $key => $relation) {
        //                 $relationships['relationships'][$relation] = [
        //                     'links' => [
        //                         'self' => route("api.{$model->getResourceType()}.relationships.{$relation}.show", $model),
        //                         'related' => route("api.{$model->getResourceType()}.{$relation}", $model),
        //                     ]
        //                 ];
        //             }
        //             $this->assertJsonFragment($relationships);
        //         }
        //         return $this;
        //     }
        // );

        TestResponse::macro(
            'assertJsonApiError',
            function (string $title = null, string $detail = null, string $status = null) {
                /** @var TestResponse $this */
                try {
                    $this->assertJsonStructure(
                        [
                            'errors' => [
                                '*' => ['title', 'detail']
                            ]
                        ]
                    );
                } catch (ExpectationFailedException $e) {
                    // } catch (\TypeError $e) {
                    Assert::fail("Error MUST be returned as an array keyed by errors in the level of a JSON:API document" . PHP_EOL . PHP_EOL . $e->getMessage());
                }

                $title && $this->assertJsonFragment(['title' => $title]);
                $detail && $this->assertJsonFragment(['detail' => $detail]);
                $status && $this->assertJsonFragment(['status' => $status])->assertStatus((int)$status);

                return $this;
            }
        );
    }
}
// Builder::macro('allowedFilters', function (array $allowedFilterFields) {
//     /** @var Builder $this */
//     foreach (request('filter', []) as $filterField => $filterValue) {

//         $allowed = false;
//         $scope = null;

//         foreach ($allowedFilterFields as $allowedFilterField) {
//             // dump($allowedFilterField);
//             if (str_contains($allowedFilterField, ':')) {
//                 [$relation, $scope] = explode(':', $allowedFilterField);
//                 // dump($relation, str_contains($allowedFilterField, '.'));
//                 if ($filterField === $relation) {
//                     dump($filterField, $relation);
//                     $allowed = true;
//                     break;
//                 }
//             } elseif ($filterField === $allowedFilterField) {
//                 $allowed = true;
//                 break;
//             }
//         }

//         if (!$allowed) {
//             $message =  "The filter field '{$filterField}' is not allowed in the '{$this->getResourceType()}' resource.";
//             throw new BadRequestHttpException($message);
//         }

//         if ($scope !== null) {
//             if ($this->hasNamedScope($scope))
//                 $this->$scope($filterValue);
//             else
//                 throw new \App\Exceptions\JsonApi\ServerErrorHttpException("Named scope '{$scope}' is not defined.");
//         } else {
//             if ($this->hasNamedScope('orWhereLike'))
//                 $this->orWhereLike($filterField, $filterValue);
//             else
//                 $this->where($filterField, 'LIKE', '%' . $filterValue . '%');
//         }
//     }

//     return $this;
// });

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Category;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CategoryMenusController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship']),
        ];
    }
    public function index(Category $category)
    {
  
        Gate::authorize('view', $category);

        $menus = $category->menus()
            ->allowedIncludes(['category'])
            ->allowedFilters(['name', 'price', 'priority', 'categories:whereHasCategories'])
            ->allowedSorts(['name', 'description', 'price', 'priority', 'stock'])
            ->sparseFieldset()
            ->jsonApiPaginate();
        return MenuResource::collection($menus);
    }

    public function showRelationship(Category $category)
    {
        Gate::authorize('view', $category);
        return MenuResource::identifiers($category->menus);
    }
}

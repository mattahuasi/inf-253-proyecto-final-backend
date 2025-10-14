<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Category::make());
        $categories = Category::sparseFieldset()
            ->allowedIncludes(['menus'])
            ->allowedFilters(['name', 'description', 'priority'])
            ->allowedSorts(['name', 'description', 'priority'])
            ->jsonApiPaginate();

        return CategoryResource::collection($categories);
    }

    public function store(SaveCategoryRequest $request)
    {
        Gate::authorize('create', Category::make());
        $category = Category::make();

        $attributes = $request->validatedAttributes();

        $category->name = $attributes['name'];
        $category->slug = $attributes['slug'];
        $category->description = $attributes['description'];
        $category->priority = $attributes['priority'];
        $category->save();

        return CategoryResource::make($category);
    }

    public function show(string $id)
    {
        $category = Category::where('slug', $id)
            ->allowedIncludes(['menus'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $category);
        return CategoryResource::make($category);
    }

    public function update(SaveCategoryRequest $request, Category $category)
    {
        Gate::authorize('update', Category::make());
        $attributes = $request->validatedAttributes();

        $category->name = $attributes['name'];
        $category->slug = $attributes['slug'];
        $category->description = $attributes['description'];
        $category->priority = $attributes['priority'];

        $category->save();
        return CategoryResource::make($category);
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete', Category::make());

        $category->delete();
        return response()->noContent();
    }
}

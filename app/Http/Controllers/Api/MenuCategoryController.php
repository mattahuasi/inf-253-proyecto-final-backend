<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class MenuCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship', 'updateRelationship']),
        ];
    }

    public function index(Menu $menu)
    {
        Gate::authorize('view', $menu);
        return CategoryResource::make($menu->category);
    }

    public function showRelationship(Menu $menu)
    {
        Gate::authorize('view', $menu);
        return CategoryResource::identifier($menu->category);
    }

    public function updateRelationship(Menu $menu, Request $request)
    {
        Gate::authorize('update', $menu);

        $request->validate([
            'data'        => 'required|array',
            'data.id'     => 'required|string|exists:categories,slug',
            'data.type'   => 'required|string|in:categories',
        ]);

        $category_slug = $request->input('data.id');
        $category = Category::where('slug', $category_slug)->first();
        $menu->category_id = $category->id;
        $menu->save();

        return CategoryResource::identifier($category);
    }
}

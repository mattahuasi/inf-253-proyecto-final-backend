<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveMenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Menu::make());

        $menus = Menu::query()
            ->allowedIncludes(['category'])
            ->allowedFilters(['name', 'price', 'priority', 'categories:whereInHasCategories']);
        // dump(getQuerySql($menus));
        $menus = $menus->allowedSorts(['name', 'description', 'price', 'priority', 'stock'])
            ->sparseFieldset()
            ->jsonApiPaginate();

        return MenuResource::collection($menus);
    }

    public function store(SaveMenuRequest $request)
    {
        Gate::authorize('create', Menu::make());

        $attributes = $request->validatedAttributes();
        $menu = Menu::create($attributes);

        return MenuResource::make($menu);
    }

    public function show(string $id): MenuResource
    {
        $menu = Menu::where('slug', $id)
            ->allowedIncludes(['category'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $menu);

        return MenuResource::make($menu);
    }

    public function update(SaveMenuRequest $request, Menu $menu)
    {
        Gate::authorize('update', Menu::make());

        $attributes = $request->validatedAttributes();
        $menu->update($attributes);

        return MenuResource::make($menu);
    }

    public function destroy(Menu $menu)
    {
        Gate::authorize('delete', Menu::make());
        $menu->delete();
        return response()->noContent();
    }

    public function updatePhoto(Request $request, Menu $menu)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoNewName = $request->file('photo')->hashName();
        $request->file('photo')->storeAs('menus/photos', $photoNewName, 'public');
        $photoOldName = $menu->photo;
        if (!empty($photoOldName) && Storage::disk('public')->exists("menus/photos/{$photoOldName}"))
            Storage::disk('public')->delete("menus/photos/{$photoOldName}");
        $menu->photo = $photoNewName;
        $menu->save();

        return MenuResource::make($menu);
    }
}

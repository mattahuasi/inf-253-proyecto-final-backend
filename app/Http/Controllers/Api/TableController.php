<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use Gate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TableController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
            // new Middleware('auth.apiKey', only: ['index', 'show']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Table::make());
        $tables = Table::sparseFieldset()
            ->allowedIncludes(['orders'])
            ->allowedFilters(['number', 'status', 'ability'])
            ->allowedSorts(['number', 'status', 'ability'])
            ->jsonApiPaginate();

        return TableResource::collection($tables);
    }

    public function store(SaveTableRequest $request)
    {
        Gate::authorize('create', Table::make());

        $attributes = $request->validatedAttributes();

        $table = Table::make();
        $table->number = $attributes['number'];
        $table->status = $attributes['status'];
        $table->ability = $attributes['ability'];
        $table->save();

        return TableResource::make($table);
    }

    public function show(string $id)
    {
        $table = Table::where('number', $id)
            ->allowedIncludes(['orders'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $table);

        return TableResource::make($table);
    }

    public function update(SaveTableRequest $request, Table $table)
    {
        Gate::authorize('update', $table);
        $attributes = $request->validatedAttributes();

        $table->number = $attributes['number'];
        $table->status = $attributes['status'];
        $table->ability = $attributes['ability'];

        $table->save();
        return TableResource::make($table);
    }

    public function destroy(Table $table)
    {
        Gate::authorize('delete', $table);
        $table->delete();
        return response()->noContent();
    }
}

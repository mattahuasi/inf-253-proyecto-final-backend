<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveStateRequest;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class StateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', State::make());

        $states = State::sparseFieldset()
            ->allowedIncludes(['orders'])
            ->allowedFilters(['name', 'description', 'color'])
            ->allowedSorts(['name', 'description', 'color'])
            ->jsonApiPaginate();

        return StateResource::collection($states);
    }

    public function store(SaveStateRequest $request)
    {
        Gate::authorize('create', State::make());

        $attributes = $request->validatedAttributes();
        $state = State::create($attributes);

        return StateResource::make($state);
    }

    public function show(string $id)
    {
        $state = State::where('slug', $id)
            ->allowedIncludes(['orders'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $state);

        return StateResource::make($state);
    }

    public function update(SaveStateRequest $request, State $state)
    {
        Gate::authorize('update', $state);

        $attributes = $request->validatedAttributes();
        $state->update($attributes);

        return StateResource::make($state);
    }

    public function destroy(State $state)
    {
        Gate::authorize('delete', $state);
        $state->delete();
        return response()->noContent();
    }
}

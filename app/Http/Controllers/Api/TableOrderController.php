<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Table;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class TableOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship']),
        ];
    }

    public function index(Table $table)
    {
        Gate::authorize('view', $table);
        $orders = $table->orders()
            ->allowedIncludes(['table'])
            ->allowedFilters(['number', 'status', 'ability'])
            ->allowedSorts(['number', 'status', 'ability'])
            ->sparseFieldset()
            ->jsonApiPaginate();

        return OrderResource::collection($orders);
    }

    public function showRelationship(Table $table)
    {
        Gate::authorize('view', $table);
        return OrderResource::identifiers($table->orders);
    }
}

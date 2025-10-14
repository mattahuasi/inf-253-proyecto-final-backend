<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePersonRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    private function modifyRequestFilters(Request $request)
    {
        $filters = $request->input('filter', []);
        foreach ($filters as $key => $value) {
            if (in_array($key, ['names', 'paternal_surname', 'maternal_surname', 'gender', 'phone'])) {
                $newKey = 'person.' . $key;
                $filters[$newKey] = $value;
                unset($filters[$key]);
            }
        }
        $request->merge(['filter' => $filters]);
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Employee::make());

        $this->modifyRequestFilters(request());

        $employees = Employee::sparseFieldset()
            ->allowedIncludes(['user'])
            ->allowedFilters(['person.names', 'person.paternal_surname', 'person.maternal_surname', 'type'])
            ->allowedSorts([])
            ->jsonApiPaginate();

        return EmployeeResource::collection($employees);
    }

    public function show(string $id)
    {
        $employee = Employee::where('person_id', $id)
            ->allowedIncludes(['user'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $employee);

        return EmployeeResource::make($employee);
    }

    public function store(SavePersonRequest $request)
    {
        Gate::authorize('create', Employee::make());
        $attributes = $request->validatedAttributes();

        $person = Person::create([
            'paternal_surname' => $attributes['paternal_surname'],
            'maternal_surname' => $attributes['maternal_surname'],
            'names' => $attributes['names'],
            'gender' => $attributes['gender'],
            'phone' => $attributes['phone'],
        ]);

        $employee = Employee::create([
            'person_id' => $person->id,
            'type' => $attributes['type']
        ]);

        return EmployeeResource::make($employee);
    }

    public function update(SavePersonRequest $request, Employee $employee)
    {
        Gate::authorize('update', $employee);
        $attributes = $request->validatedAttributes();

        $employee->person->update([
            'paternal_surname' => $attributes['paternal_surname'],
            'maternal_surname' => $attributes['maternal_surname'],
            'names' => $attributes['names'],
            'gender' => $attributes['gender'],
            'phone' => $attributes['phone'],
        ]);

        $employee->update([
            'type' => $attributes['type']
        ]);

        return EmployeeResource::make($employee);
    }

    public function destroy(Employee $employee)
    {
        Gate::authorize('delete', Employee::make());
        $employee->person->delete();
        $employee->delete();

        return response()->noContent();
    }
}

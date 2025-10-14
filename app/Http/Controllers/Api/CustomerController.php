<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePersonRequest;
use App\Http\Requests\SaveUserRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller implements HasMiddleware
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
        Gate::authorize('viewAny', Customer::make());
        $this->modifyRequestFilters(request());

        $customers = Customer::sparseFieldset()
            ->allowedIncludes(['user'])
            ->allowedFilters(['person.names', 'person.paternal_surname', 'person.maternal_surname', 'user.name'])
            ->allowedSorts([])
            ->jsonApiPaginate();

        return CustomerResource::collection($customers);
    }

    public function show(string $id)
    {
        $customer = Customer::where('person_id', $id)
            ->allowedIncludes(['user'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $customer);

        return CustomerResource::make($customer);
    }

    public function store(SavePersonRequest $request)
    {
        Gate::authorize('create', Customer::make());
        $attributes = $request->validatedAttributes();

        $person = Person::create([
            'paternal_surname' => $attributes['paternal_surname'],
            'maternal_surname' => $attributes['maternal_surname'],
            'names' => $attributes['names'],
            'gender' => $attributes['gender'],
            'phone' => $attributes['phone'],
        ]);

        $customer = Customer::create([
            'person_id' => $person->id,
        ]);

        return CustomerResource::make($customer);
    }

    public function update(SavePersonRequest $request, Customer $customer)
    {
        Gate::authorize('update', $customer);
        $attributes = $request->validatedAttributes();

        $customer->person->update([
            'paternal_surname' => $attributes['paternal_surname'],
            'maternal_surname' => $attributes['maternal_surname'],
            'names' => $attributes['names'],
            'gender' => $attributes['gender'],
            'phone' => $attributes['phone'],
        ]);

        return CustomerResource::make($customer);
    }

    public function destroy(Customer $customer)
    {
        Gate::authorize('delete', Customer::make());
        $customer->person->delete();
        $customer->delete();

        return response()->noContent();
    }

}

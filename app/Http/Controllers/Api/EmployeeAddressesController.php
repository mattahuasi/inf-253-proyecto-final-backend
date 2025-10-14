<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeeAddressesController extends Controller  implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(Employee $employee)
    {
        Gate::authorize('viewAny', $employee);

        $addresses = $employee->person->addresses()
            ->allowedFilters(['zona', 'street', 'detail'])
            ->allowedSorts(['zona', 'street', 'detail'])
            ->sparseFieldset()
            ->jsonApiPaginate();

        return AddressResource::collection($addresses);
    }

    public function show(Employee $employee, string $addressId)
    {
        $address = $employee->person->addresses()
            ->where('id', $addressId)
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $employee);

        return AddressResource::make($address);
    }

    public function store(Request $request, Employee $employee)
    {
        Gate::authorize('create', $employee);
        $data = $request->validate([
            'data.type' => 'required|in:addresses',
            'data.attributes.zona' => 'required|string|min:3|max:90',
            'data.attributes.street' => "required|string|min:3|max:45",
            'data.attributes.detail' => "nullable|string|min:8|max:180"
        ]);

        $attributes = $data['data']['attributes'];
        $address = Address::make($attributes);
        $employee->person->addresses()->save($address);

        return AddressResource::make($address);
    }

    public function update(Request $request, Employee $employee, Address $address)
    {
        Gate::authorize('update', $employee);

        if ($employee->person_id != $address->person_id)
            throw new NotFoundHttpException();

        $data = $request->validate([
            'data.type' => 'required|in:addresses',
            'data.id' => 'required|string|exists:addresses,id',
            'data.attributes.zona' => 'required|string|min:3|max:90',
            'data.attributes.street' => "required|string|min:3|max:45",
            'data.attributes.detail' => "nullable|string|min:8|max:180"
        ]);

        $attributes = $data['data']['attributes'];
        $address->update($attributes);

        return AddressResource::make($address);
    }

    public function destroy(Employee $employee, Address $address)
    {
        Gate::authorize('delete', $employee);

        if ($employee->person_id != $address->person_id)
            throw new NotFoundHttpException();

        $employee->person->addresses()->where('id', $address->id)->delete();

        return response()->noContent();
    }

    // public function update(Request $request, Employee $employee)
    // {
    //     $validated = $request->validate([
    //         'data'        => 'required|array',
    //         'data.*.id'   => 'required|string|exists:addresses,id',
    //         'data.*.type' => 'required|string|in:addresses',
    //     ]);

    //     $addressIds = collect($validated['data'])->pluck('id');
    //     $employee->person->addresses()->delete();

    //     $addresses = Address::whereIn('id', $addressIds)->get();
    //     $employee->person->addresses()->saveMany($addresses);

    //     return AddressResource::identifiers($employee->person->addresses);
    // }

    // public function attach(Request $request, Employee $employee)
    // {
    //     $validated = $request->validate([
    //         'data'        => 'required|array',
    //         'data.*.id'   => 'required|string|exists:addresses,id',
    //         'data.*.type' => 'required|string|in:addresses',
    //     ]);

    //     $addressIds = collect($validated['data'])->pluck('id');
    //     $addresses = Address::whereIn('id', $addressIds)->where('person_id', '!=', $employee->person_id)->get();

    //     $employee->person->addresses()->saveMany($addresses);

    //     return response()->noContent();
    // }

    // public function detach(Request $request, Employee $employee)
    // {
    //     $validated = $request->validate([
    //         'data'        => 'required|array',
    //         'data.*.id'   => 'required|string|exists:addresses,id',
    //         'data.*.type' => 'required|string|in:addresses',
    //     ]);

    //     $addressIds = collect($validated['data'])->pluck('id');

    //     $employee->person->addresses()->whereIn('id', $addressIds)->delete();

    //     return response()->noContent();
    // }

    public function showRelationship(Employee $employee)
    {
        Gate::authorize('viewAny', $employee);
        return AddressResource::identifiers($employee->person->addresses);
    }
}

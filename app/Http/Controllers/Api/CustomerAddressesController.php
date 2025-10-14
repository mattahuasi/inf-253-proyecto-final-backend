<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerAddressesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(Customer $customer)
    {
        Gate::authorize('viewAny', $customer);

        $addresses = $customer->person->addresses()
            ->allowedFilters(['zona', 'street', 'detail'])
            ->allowedSorts(['zona', 'street', 'detail'])
            ->sparseFieldset()
            ->jsonApiPaginate();

        return AddressResource::collection($addresses);
    }

    public function show(Customer $customer, string $addressId)
    {
        $address = $customer->person->addresses()
            ->where('id', $addressId)
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $customer);

        return AddressResource::make($address);
    }

    public function store(Request $request, Customer $customer)
    {
        Gate::authorize('create', $customer);
        
        $data = $request->validate([
            'data.type' => 'required|in:addresses',
            'data.attributes.zona' => 'required|string|min:3|max:90',
            'data.attributes.street' => "required|string|min:3|max:45",
            'data.attributes.detail' => "nullable|string|min:8|max:180"
        ]);

        $attributes = $data['data']['attributes'];
        $address = Address::make($attributes);
        $customer->person->addresses()->save($address);

        return AddressResource::make($address);
    }

    public function update(Request $request, Customer $customer, Address $address)
    {
        Gate::authorize('update', $customer);

        if ($customer->person_id != $address->person_id)
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

    public function destroy(Customer $customer, Address $address)
    {
        Gate::authorize('delete', $customer);

        if ($customer->person_id != $address->person_id)
            throw new NotFoundHttpException();

        $customer->person->addresses()->where('id', $address->id)->delete();

        return response()->noContent();
    }

    // public function update(Request $request, Customer $customer)
    // {
    //     $validated = $request->validate([
    //         'data'        => 'required|array',
    //         'data.*.id'   => 'required|string|exists:addresses,id',
    //         'data.*.type' => 'required|string|in:addresses',
    //     ]);

    //     $addressIds = collect($validated['data'])->pluck('id');
    //     $customer->person->addresses()->delete();

    //     $addresses = Address::whereIn('id', $addressIds)->get();
    //     $customer->person->addresses()->saveMany($addresses);

    //     return AddressResource::identifiers($customer->person->addresses);
    // }

    // public function attach(Request $request, Customer $customer)
    // {
    //     $validated = $request->validate([
    //         'data'        => 'required|array',
    //         'data.*.id'   => 'required|string|exists:addresses,id',
    //         'data.*.type' => 'required|string|in:addresses',
    //     ]);

    //     $addressIds = collect($validated['data'])->pluck('id');
    //     $addresses = Address::whereIn('id', $addressIds)->where('person_id', '!=', $customer->person_id)->get();

    //     $customer->person->addresses()->saveMany($addresses);

    //     return response()->noContent();
    // }

    // public function detach(Request $request, Customer $customer)
    // {
    //     $validated = $request->validate([
    //         'data'        => 'required|array',
    //         'data.*.id'   => 'required|string|exists:addresses,id',
    //         'data.*.type' => 'required|string|in:addresses',
    //     ]);

    //     $addressIds = collect($validated['data'])->pluck('id');

    //     $customer->person->addresses()->whereIn('id', $addressIds)->delete();

    //     return response()->noContent();
    // }

    public function showRelationship(Customer $customer)
    {
        Gate::authorize('viewAny', $customer);
        return AddressResource::identifiers($customer->person->addresses);
    }
}

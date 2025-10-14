<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthResource;
use App\Http\Responses\TokenResponse;
use App\Models\Customer;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['logout', 'meShow', 'meUpdate']),
            new Middleware('guest:sanctum', only: ['login', 'register']),
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_name' => 'required|string'
        ]);

        $user = User::whereEmail($request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => [__('auth.failed')]]);
        }

        return new TokenResponse($user);
    }

    public function logout(Request $request)
    {
        return $request->user()->currentAccessToken()->delete();
    }

    public function meShow(Request $request)
    {
        return $this->getResponseUserAuth($request->user());
    }

    public function meUpdate(Request $request)
    {
        $attributes = $request->validate([
            'username' => 'required|string|min:3|max:45',
            'email' => 'required|email|min:3|max:180|unique:users,email,' . $request->user()->id . ',id',
            "paternal_surname" => "nullable|string|min:3|max:20|required_without:data.attributes.maternal_surname",
            "maternal_surname" => "nullable|string|min:3|max:20|required_without:data.attributes.paternal_surname",
            'names' => 'required|string|min:3|max:45',
            'gender' => "required|in:M,F",
            'phone' => "nullable|string|min:8|max:15",
        ]);

        $user = $request->user();

        $user->person->update([
            'paternal_surname' => $attributes['paternal_surname'],
            'maternal_surname' => $attributes['maternal_surname'],
            'names' => $attributes['names'],
            'gender' => $attributes['gender'],
            'phone' => $attributes['phone'] ?? $user->phone,
        ]);

        $user->update([
            'username' => $attributes['username'],
            'email' => $attributes['email'],
        ]);

        return $this->getResponseUserAuth($request->user());
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:45',
            'email' => 'required|email|min:3|max:180|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'device_name' => 'required|string|max:90',
            "paternal_surname" => "nullable|string|min:3|max:20|required_without:data.attributes.maternal_surname",
            "maternal_surname" => "nullable|string|min:3|max:20|required_without:data.attributes.paternal_surname",
            'names' => 'required|string|min:3|max:45',
            'gender' => "required|in:M,F",
            'phone' => "nullable|string|min:8|max:15",
        ]);

        $person = Person::create([
            'paternal_surname' => $request->paternal_surname,
            'maternal_surname' => $request->maternal_surname,
            'names' => $request->names,
            'gender' => $request->gender,
            'phone' => $request?->phone,
        ]);
        
        $customer = Customer::create(['person_id' => $person->id]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'Cliente')->firstOrCreate()->id,
            'person_id' => $customer->person_id
        ]);

        return new TokenResponse($user);
    }

    private function getResponseUserAuth(User $user)
    {
        return [
            'username' => $user->username,
            'email' => $user->email,
            'user_type' => $user->person->type,
            'role' => $user->role->name,
            'paternal_surname' => $user->person->paternal_surname,
            'maternal_surname' => $user->person->maternal_surname,
            'names' => $user->person->names,
            'gender' => $user->person->gender,
            'phone' => $user->person->phone
        ];
    }
}

<?php

namespace Tests\Traits;

use App\Models\Person;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait AuthUser
{
    /**
     * Método para autenticar un usuario con permisos específicos.
     *
     * @param array $permissions
     * @return User
     */
    public function authenticateUser(array $permissions): User
    {
        $person = Person::factory()->create([
            'paternal_surname' => 'TEST',
            'maternal_surname' => 'TEST',
            'names' => 'TEST',
        ]);
        $user = User::factory()->create(['person_id' => $person->id]);
        $user->role->name = 'role A';
        $user->role->save();
        Sanctum::actingAs($user, $permissions);
        return $user;
    }
}

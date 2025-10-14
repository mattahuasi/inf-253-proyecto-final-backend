<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticatedUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_fetch_authenticated_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('api.auth.me.show'))
            ->assertJson([
                "username" => $user->username,
                "email" =>  $user->email,
                "user_type" =>  $user->person->type,
                "role" =>  $user->role->name,
                "paternal_surname" =>  $user->person->paternal_surname,
                "maternal_surname" =>  $user->person->maternal_surname,
                "names" =>   $user->person->names,
                "gender" =>  $user->person->gender,
                "phone" =>   $user->person->phone
            ]);
    }

    #[Test]
    public function can_update_authenticated_user(): void
    {
        $this->withoutExceptionHandling();
        $this->withoutJsonApiDocumentFormatting();
        
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            "username" => "Berta Ayala Hijo",
            "email"    => $user->email,
            "paternal_surname" => "Lorente",
            "maternal_surname" => "Aguilar",
            "names" => "Miguel",
            "gender" => "M",
            // "phone" => "76525055",
        ];

        $this->patchJson(route('api.auth.me.update'), $data)
            ->assertJson([
                "username" => "Berta Ayala Hijo",
                "email"    => $user->email,
                "paternal_surname" => "Lorente",
                "maternal_surname" => "Aguilar",
                "names" => "Miguel",
                "gender" => "M",
                "phone" => $user->person->phone,
            ]);
    }

    #[Test]
    public function guest_cannot_fetch_or_update_any_user(): void
    {
        $this->patchJson(route('api.auth.me.update'))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

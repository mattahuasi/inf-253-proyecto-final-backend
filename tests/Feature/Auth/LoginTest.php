<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_login(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data =  $this->validCredentials(['email' => $user->email]);

        $response = $this->postJson(route('api.auth.login'), $data);

        $token = PersonalAccessToken::findToken($response->json('plain_text_token'));

        $this->assertTrue($token->tokenable->is($user));
    }

    #[Test]
    public function cannot_login_twice(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $token = $user->createToken($user->username)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.auth.login'))
            ->assertNoContent();
    }


    #[Test]
    public function use_permissions_are_assigned_As_abilities_to_the_token(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $p1 = Permission::factory()->create();
        $p2 = Permission::factory()->create();
        $p3 = Permission::factory()->create();

        $user->role->givePermissionTo($p1);
        $user->role->givePermissionTo($p2);

        $data =  $this->validCredentials(['email' => $user->email]);

        $response = $this->postJson(route('api.auth.login'), $data);

        $token = $response->json('plain_text_token');

        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->can($p1->name));
        $this->assertTrue($dbToken->can($p2->name));
        $this->assertFalse($dbToken->can($p3->name));
    }

    #[Test]
    public function password_must_be_valid(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response = $this->postJson(route('api.auth.login'), $data);

        $response->assertJsonValidationErrorFor('email');
    }

    #[Test]
    public function user_must_be_registered(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials();

        $response = $this->postJson(route('api.auth.login'), $data);

        $response->assertJsonValidationErrorFor('email');
    }

    #[Test]
    public function email_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['email' => null]);

        $response = $this->postJson(route('api.auth.login'), $data);

        $response->assertSee(__('validation.required', ['attribute' => 'email']))
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function email_must_be_valid(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['email' => 'invalid-email']);

        $response = $this->postJson(route('api.auth.login'), $data);

        $errorEmail = json_decode(__('validation.email', ['attribute' => 'email']));

        $response->assertSee($errorEmail)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function password_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['password' => null]);

        $response = $this->postJson(route('api.auth.login'), $data);

        $response->assertSee(__('validation.required', ['attribute' => 'password']))
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function device_name_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['device_name' => null]);

        $response = $this->postJson(route('api.auth.login'), $data);

        $errorDeviceName = __('validation.required', ['attribute' => 'device name']);

        $response->assertSee($errorDeviceName)
            ->assertJsonValidationErrors(['device_name']);
    }

    protected function validCredentials(mixed $override = []): array
    {
        return array_merge([
            'email' => 'test@gmail.com',
            'password' => 'password',
            'device_name' => 'Device Test'
        ], $override);
    }
}

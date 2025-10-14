<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterCustomerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_register(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $this->withoutExceptionHandling();

        Role::factory()->create([
            'name' => 'Cliente'
        ]);

        $data = $this->validData();

        $response = $this->postJson(route('api.auth.register'), $data);

        $token = PersonalAccessToken::findToken($response->json('plain_text_token'));

        $user = User::first();

        $this->assertTrue($token->tokenable->is($user));

        $this->assertDatabaseHas('users', [
            'username' => 'test_name',
            'email' => 'user_test@test.com'
        ]);

        $this->assertDatabaseHas('people', [
            'paternal_surname' => 'mamani',
            'maternal_surname' => 'calle',
            'names' => 'martin edwin',
            'gender' => 'M',
            'phone' => '+591 00000000',
        ]);

        $this->assertDatabaseHas('customers', [
            'person_id' => $user->person_id
        ]);
    }


    #[Test]
    public function email_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validData(['email' => null]);

        $response = $this->postJson(route('api.auth.register'), $data);

        $response->assertSee(__('validation.required', ['attribute' => 'email']))
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function email_must_be_valid(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validData(['email' => 'invalid-email']);

        $response = $this->postJson(route('api.auth.register'), $data);

        $errorEmail = json_decode(__('validation.email', ['attribute' => 'email']));

        $response->assertSee($errorEmail)
            ->assertJsonValidationErrors(['email']);
    }


    #[Test]
    public function email_must_be_unique(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validData(['email' => $user->email]);

        $response = $this->postJson(route('api.auth.register'), $data);

        $errorEmail = json_decode(__('validation.unique', ['attribute' => 'email']));

        $response->assertSee($errorEmail)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function password_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validData(['password' => null]);

        $response = $this->postJson(route('api.auth.register'), $data);

        $response->assertSee(__('validation.required', ['attribute' => 'password']))
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function password_must_be_confirmed(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validData(['password_confirmation' => 'other-password']);

        $response = $this->postJson(route('api.auth.register'), $data);

        $errorPassword = json_decode(__('validation.confirmed', ['attribute' => 'password']));

        $response->assertSee($errorPassword)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function device_name_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validData(['device_name' => null]);

        $response = $this->postJson(route('api.auth.register'), $data);

        $errorDeviceName = __('validation.required', ['attribute' => 'device name']);

        $response->assertSee($errorDeviceName)
            ->assertJsonValidationErrors(['device_name']);
    }

    protected function validData(mixed $override = []): array
    {
        return array_merge([
            'paternal_surname' => 'Mamani',
            'maternal_surname' => 'Calle',
            'names' => 'Martin Edwin',
            'gender' => 'M',
            'phone' => '+591 00000000',
            'username' => 'test_name',
            'email' => 'user_test@test.com',
            'device_name' => 'App test Laravel',
            'password' => 'password',
            'password_confirmation' => 'password'
        ], $override);
    }
}

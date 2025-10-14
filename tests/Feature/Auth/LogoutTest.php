<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_logout(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $token = $user->createToken($user->username)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.auth.logout'));

        $this->assertNull(PersonalAccessToken::findToken($token));
    }
}

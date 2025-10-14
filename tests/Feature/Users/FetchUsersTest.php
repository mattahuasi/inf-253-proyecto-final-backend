<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchUsersTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_user(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['user:show']);

        $user = User::factory()->create();

        $response = $this->getJson(route('api.users.show', $user));

        $response->assertJsonApiResource($user, [
            'username' => $user->username,
            'email' => $user->email
        ]);

        $response->assertJsonApiRelationshipLinks($user, ['role']);
    }

    #[Test]
    public function can_fetch_all_users(): void
    {
        $this->withoutExceptionHandling();

        $users[] = $this->authenticateUser(['user:index']);
        $users[] = User::factory()->create();
        $users[] = User::factory()->create();
        $users[] = User::factory()->create();

        $response = $this->getJson(route('api.users.index'));
        $response->assertJsonApiResourceCollection($users, [
            'username',
            'email',
            'user_type'
        ]);
        $response->assertJsonApiCollectionRelationshipLinks($users, ['role']);
    }

    #[Test]
    public function it_returns_a_json_api_error_object_when_an_user_is_not_found(): void
    {
        $this->authenticateUser(['user:show']);

        $url = route('api.users.show', [
            'user' => 'not-existing'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.404'),
                detail: "No records found with the id 'not-existing' in the 'users' resource.",
                status: "404"
            );
    }
}

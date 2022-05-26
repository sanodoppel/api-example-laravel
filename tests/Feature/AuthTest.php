<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\JsonResponse;
use Tests\FeatureTestCase;

class AuthTest extends FeatureTestCase
{
    /**
     * @return string
     */
    public function testLogin(): string
    {
        $user = User::find(1);
        $response = $this->postJson(
            route('api_auth_login', [], false),
            ['email' => $user->email, 'password' => UserFactory::PASSWORD]
        );
        $response->assertStatus(JsonResponse::HTTP_OK);
        $token = $response->json()['result']['accessToken'];
        $this->assertIsString($token);

        return $token;
    }

    /**
     * @depends testLogin
     * @param string $token
     * @return string
     */
    public function testRefresh(string $token): string
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson(
            route('api_auth_refresh', [], false),
            []
        );
        $response->assertStatus(JsonResponse::HTTP_OK);
        $newToken = $response->json()['result']['accessToken'];
        $this->assertIsString($newToken);

        $this->refreshApplication();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson(
            route('api_auth_refresh', [], false),
            []
        );
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);

        return $newToken;
    }

    /**
     * @depends testRefresh
     * @param string $token
     * @return void
     */
    public function testLogout(string $token)
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson(
            route('api_auth_logout', [], false),
            []
        );
        $response->assertStatus(JsonResponse::HTTP_OK);

        $this->refreshApplication();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson(
            route('api_auth_logout', [], false),
            []
        );
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }
}

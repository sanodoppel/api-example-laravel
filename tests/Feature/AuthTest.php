<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\JsonResponse;
use Tests\FeatureTestCase;

class AuthTest extends FeatureTestCase
{
    /**
     * @return array
     */
    public function testLogin(): array
    {
        $user = User::find(1);
        $response = $this->postJson(
            route('api_auth_login', [], false),
            ['email' => $user->email, 'password' => UserFactory::PASSWORD]
        );
        $response->assertStatus(JsonResponse::HTTP_OK);
        $responseData = $response->json();
        $token = $response->json()['result']['accessToken'];
        $refreshToken = $responseData['result']['refreshToken'];
        $fingerprint = $responseData['result']['fingerprint'];

        $this->assertNotEmpty($token);
        $this->assertNotEmpty($refreshToken);
        $this->assertNotEmpty($fingerprint);

        return ['refreshToken' => $refreshToken, 'fingerprint' => $fingerprint];
    }

    /**
     * @depends testLogin
     * @param array $refreshData
     */
    public function testRefresh(array $refreshData)
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $refreshData['refreshToken'])->postJson(
            route('api_auth_refresh', [], false),
            ['fingerprint' => $refreshData['fingerprint']]
        );
        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertNotEmpty($response->json()['result']['accessToken']);
        $this->assertEquals($refreshData['fingerprint'], $response->json()['result']['fingerprint']);

        $this->refreshApplication();
        $response = $this->withHeader('Authorization', 'Bearer ' . $refreshData['refreshToken'])->postJson(
            route('api_auth_refresh', [], false),
            ['fingerprint' => $refreshData['fingerprint']]
        );
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $user = User::find(1);
        $response = $this->postJson(
            route('api_auth_login', [], false),
            ['email' => $user->email, 'password' => UserFactory::PASSWORD]
        );

        $fingerprint = $response->json()['result']['fingerprint'];

        $response = $this->postJson(
            route('api_auth_logout', [], false),
            ['fingerprint' => $fingerprint]
        );
        $response->assertStatus(JsonResponse::HTTP_OK);

        $this->refreshApplication();
        $response = $this->postJson(
            route('api_auth_logout', [], false),
            ['fingerprint' => $fingerprint]
        );
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }
}

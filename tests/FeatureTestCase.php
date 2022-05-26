<?php

namespace Tests;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class FeatureTestCase extends BaseTestCase
{
    use CreatesApplication;

    public function addAuthorizationHeader(): static
    {
        $user = User::find(1);
        $response = $this
            ->postJson(
                route('api_auth_login', [], false),
                ['email' => $user->email, 'password' => UserFactory::PASSWORD]
            );

        $token = $response->json()['result']['accessToken'];
        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }
}

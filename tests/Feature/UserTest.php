<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use JetBrains\PhpStorm\ArrayShape;
use Tests\FeatureTestCase;

class UserTest extends FeatureTestCase
{

    /**
     * @return string
     */
    public function testSignIn(): string
    {
        $user = User::find(1);
        $response = $this->postJson(
            route('api_user_sign_in', [], false),
            ['email' => $user->email, 'password' => UserFactory::PASSWORD]
        );
        $response->assertStatus(JsonResponse::HTTP_OK);
        $token = $response->json()['result']['accessToken'];
        $this->assertIsString($token);

        return $token;
    }

    /**
     * @return array
     */
    #[ArrayShape(['email' => "string", 'name' => "string", 'password' => "string", 'nickname' => "string"])]
    public function testCreateUser(): array
    {

        $data = [
            'email' => 'test@example.com',
            'firstName' => 'test',
            'lastName' => 'test',
            'password' => '123456qwerty',
            'phone' => '123456789'
        ];

        $response = $this->postJson(route('api_user_register', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_CREATED);

        // duplicated
        $response = $this->postJson(route('api_user_register', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        return $data;
    }

    public function testRecoverPassword()
    {
        // not existing email
        $data = [
            'email' => 'not_exist_email@example.test',
        ];
        $response = $this->postJson(route('api_user_recover_password', ['field' => 'email'], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        //ok
        $user = User::factory()->create();
        $data['email'] = $user->email;
        $response = $this->postJson(route('api_user_recover_password', ['field' => 'email'], false), $data);
        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function testResetPassword()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $data = [
            'email' => $user->email,
            'password' => 'testpassword',
            'token' => 'test'
        ];

        //wrong token send
        $response = $this->postJson(route('api_user_reset_passport', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        //ok
        $data['token'] = $token;
        $response = $this->postJson(route('api_user_reset_passport', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $token = $response->json()['result']['accessToken'];
        $this->assertIsString($token);

        //duplicated
        $response = $this->postJson(route('api_user_reset_passport', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }
}

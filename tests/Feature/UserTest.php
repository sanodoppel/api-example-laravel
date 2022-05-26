<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use JetBrains\PhpStorm\ArrayShape;
use Tests\FeatureTestCase;

class UserTest extends FeatureTestCase
{
    /**
     * A basic test example.
     *
     * @return array
     */
    #[ArrayShape(['email' => "string", 'name' => "string", 'password' => "string", 'nickname' => "string"])]
    public function testCreateUser(): array
    {

        $data = [
            'email' => 'test@example.com',
            'name' => 'test test',
            'password' => '123456qwerty',
            'nickname' => 'test'
        ];

        $response = $this->postJson(route('api_user_create', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_CREATED);

        // duplicated
        $response = $this->postJson(route('api_user_create', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        return $data;
    }

    /**
     * @depends testCreateUser
     */
    public function testValidateField(array $data)
    {
        $response = $this->postJson(route('api_user_validate_field', ['field' => 'email'], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        $response = $this->postJson(route('api_user_validate_field', ['field' => 'nickname'], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testForgetPassword()
    {
        // not existing email
        $data = [
            'email' => 'not_exist_email@example.test',
        ];
        $response = $this->postJson(route('api_auth_password_forget', ['field' => 'email'], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        //ok
        $user = User::factory()->create();
        $data['email'] = $user->email;
        $response = $this->postJson(route('api_auth_password_forget', ['field' => 'email'], false), $data);
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
        $response = $this->postJson(route('api_auth_password_reset', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        //ok
        $data['token'] = $token;
        $response = $this->postJson(route('api_auth_password_reset', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $token = $response->json()['result']['accessToken'];
        $this->assertIsString($token);

        //duplicated
        $response = $this->postJson(route('api_auth_password_reset', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }
}

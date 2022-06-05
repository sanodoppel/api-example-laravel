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
        $response = $this->postJson(route('api_user_password_forget', ['field' => 'email'], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        //ok
        $user = User::factory()->create();
        $data['email'] = $user->email;
        $response = $this->postJson(route('api_user_password_forget', ['field' => 'email'], false), $data);
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
        $response = $this->postJson(route('api_user_password_reset', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);

        //ok
        $data['token'] = $token;
        $response = $this->postJson(route('api_user_password_reset', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $token = $response->json()['result']['accessToken'];
        $this->assertIsString($token);

        //duplicated
        $response = $this->postJson(route('api_user_password_reset', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testGet()
    {
        $response = $this->addAuthorizationHeader()->get(route('api_user_get', [], false));
        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertEquals(User::find(1)->uuid, $response->json()['result']['id']);
    }


    public function testUpdate()
    {
        $data = [
            'email' => 'new_email@test.local',
            'name' => 'new name',
            'nickname' => 'new_nickname'
        ];

        $user = User::factory()->create();
        $response = $this->addAuthorizationHeader($user)->putJson(route('api_user_update', [], false), $data);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertEquals($user->uuid, $response->json()['result']['id']);
        $this->assertEquals($data['email'], $response->json()['result']['email']);
    }

    public function testChangePassword()
    {
        $data = [
            'currentPassword' => UserFactory::PASSWORD . 'wrong',
            'newPassword' => 'newPassword'
        ];

        $user = User::factory()->create();

        // wrong current password
        $response = $this->addAuthorizationHeader($user)
            ->postJson(route('api_user_password_change', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);

        //ok
        $data['currentPassword'] = UserFactory::PASSWORD;
        $response = $this->addAuthorizationHeader($user)
            ->postJson(route('api_user_password_change', [], false), $data);
        $response->assertStatus(JsonResponse::HTTP_OK);
    }
}

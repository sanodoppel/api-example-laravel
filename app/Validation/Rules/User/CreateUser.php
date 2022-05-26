<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

class CreateUser extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['name' => "string[]", 'nickname' => "string[]", 'email' => "string[]", 'password' => "string[]"])]
    public static function rules(): array
    {
        return array_merge(
            [
                'name' => ['required', 'regex:/^[a-zA-Z]+ [a-zA-Z]+$/'],
                'password' => ['required', 'between:8,45'],
            ],
            ValidateUserNickname::rules(),
            ValidateUserEmail::rules()
        );
    }
}

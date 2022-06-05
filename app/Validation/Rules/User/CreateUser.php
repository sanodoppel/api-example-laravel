<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

abstract class CreateUser extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['name' => "string[]", 'nickname' => "string[]", 'email' => "string[]", 'password' => "string[]"])]
    public static function rules(): array
    {
        return array_merge(
            [
                'name' => Rule::NAME_RULE,
                'password' => Rule::PASSWORD_RULE,
            ],
            ValidateUserNickname::rules(),
            ValidateUserEmail::rules()
        );
    }
}

<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

abstract class ResetPassword extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['email' => "string[]", 'token' => "string[]", 'password' => "string[]"])]
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users', 'string'],
            'token' => ['required', 'string'],
            'password' => Rule::PASSWORD_RULE,
        ];
    }
}

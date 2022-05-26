<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ResetPassword extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['email' => "string[]", 'token' => "string[]", 'password' => "string[]"])]
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users'],
            'token' => ['required'],
            'password' => ['required', 'between:8,45'],
        ];
    }
}

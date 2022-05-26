<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ForgetPassword extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['email' => "string[]"])]
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users'],
        ];
    }
}

<?php


namespace App\Validation\Rules;

use JetBrains\PhpStorm\ArrayShape;

abstract class Auth extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['email' => "string[]", 'password' => "string[]"])]
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'between:6,45'],
        ];
    }
}

<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

abstract class CreateUser extends Rule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users', 'string'],
            'phone' => ['required', 'digits_between:6,14', 'string'],
            'password' => ['required', 'between:6,45', 'string'],
        ];
    }
}

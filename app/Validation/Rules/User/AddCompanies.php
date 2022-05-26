<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;

abstract class AddCompanies extends Rule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            '*.title' => ['required', 'string'],
            '*.phone' => ['required', 'digits_between:6,15', 'string'],
        ];
    }
}

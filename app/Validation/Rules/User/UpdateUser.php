<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

abstract class UpdateUser extends Rule
{
    /**
     * @return array
     */


    #[ArrayShape([
        'name' => "string[]",
        'email' => "string[]",
        'nickname' => "string[]",
        'timezone' => "string[]"])]
    public static function rules(): array
    {
        return [
            'name' => Rule::NAME_RULE,
            'email' => ['required', 'email', sprintf('unique:users,email,%s', auth()->user()->id), 'string'],
            'nickname' => ['required', 'alpha_dash', sprintf('unique:users,nickname,%s', auth()->user()->id), 'string'],
            'timezone' => ['nullable', 'timezone', 'string']
        ];
    }
}

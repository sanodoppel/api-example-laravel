<?php

namespace App\Validation\Rules;

use App\Validation\Messages\FormMessageEnum;
use App\Validation\RuleInterface;
use JetBrains\PhpStorm\ArrayShape;

abstract class Rule implements RuleInterface
{
    protected const PASSWORD_RULE = ['required', 'between:8,45', 'string'];
    protected const NAME_RULE = [
        'required',
        'regex:/^([a-zA-Z]{2,}\s[a-zA-Z]{1,}\'?-?[a-zA-Z]{1,}\s?([a-zA-Z]{1,})?)/',
        'string'
    ];

    abstract public static function rules(): array;

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [
            'required' => FormMessageEnum::REQUIRED->name,
            'email' => FormMessageEnum::NOT_EMAIL->name,
            'between' => FormMessageEnum::WRONG_LENGTH->name,
            'regex' => FormMessageEnum::WRONG_FORMAT->name,
            'unique' => FormMessageEnum::NOT_UNIQUE->name,
            'exists' => FormMessageEnum::NOT_EXIST->name,
            'string' => FormMessageEnum::NOT_STRING->name,
            'digits_between' => FormMessageEnum::DIGIT_WRONG_LENGTH->name,
            'uuid' => FormMessageEnum::NOT_UUID->name,
            'alpha_dash' => FormMessageEnum::WRONG_FORMAT->name,
            'timezone' => FormMessageEnum::WRONG_FORMAT->name,
            'image' => FormMessageEnum::NOT_IMAGE->name,
            'max' => FormMessageEnum::WRONG_SIZE->name,
        ];
    }
}

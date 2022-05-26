<?php

namespace App\Validation;

use App\DataTransferObjects\DataTransferObject;
use Illuminate\Support\Facades\Validator;

class Validation
{
    protected ?array $errors;

    /**
     * @param array $rules
     * @param array $messages
     */
    public function __construct(protected array $rules, protected array $messages = [])
    {
    }

    /**
     * @param DataTransferObject $data
     * @return bool
     */
    public function validate(DataTransferObject $data): bool
    {
        $validator = Validator::make($data->getData(), $this->rules, $this->messages);
        if ($validator->fails()) {
            $this->errors = $validator->getMessageBag()->messages();

            return false;
        }

        $this->errors = [];

        return true;
    }

    /**
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}

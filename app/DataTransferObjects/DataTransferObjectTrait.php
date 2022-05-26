<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

trait DataTransferObjectTrait
{
    /**
     * @param Request|null $request
     */
    public function __construct(?Request $request = null)
    {
        if ($request) {
            $this->setData($request->request->all());
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        foreach ($data as $key => $item) {
            if (in_array($key, array_keys(get_class_vars(get_class($this))))) {
                $this->$key = $item;
            }
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}

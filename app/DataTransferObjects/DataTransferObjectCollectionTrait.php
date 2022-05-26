<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

trait DataTransferObjectCollectionTrait
{
    protected readonly array $data;

    protected readonly string $dtoName;

    /**
     * @param string $dtoName
     * @param Request|null $request
     */
    public function __construct(string $dtoName, ?Request $request = null)
    {
        $this->dtoName = $dtoName;
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
        $result = [];
        foreach ($data as $dtoItem) {
            $dto = new $this->dtoName();
            $dto->setData($dtoItem);

            $result[] = $dto;
        }

        $this->data = $result;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        foreach ($this->data as $dtoItem) {
            $data[] = $dtoItem->getData();
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getDataForModel(): array
    {
        $data = [];
        foreach ($this->data as $dtoItem) {
            $data[] = $dtoItem->getDataForModel();
        }

        return $data;
    }
}

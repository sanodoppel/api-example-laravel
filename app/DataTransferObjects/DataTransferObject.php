<?php

namespace App\DataTransferObjects;

interface DataTransferObject
{
    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return array
     */
    public function getDataForModel(): array;
}

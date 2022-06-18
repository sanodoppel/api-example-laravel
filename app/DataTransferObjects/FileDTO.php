<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?UploadedFile $file;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->file = $request->files->get('file');
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
}

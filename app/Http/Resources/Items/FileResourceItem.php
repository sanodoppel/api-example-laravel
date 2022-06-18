<?php

namespace App\Http\Resources\Items;

use App\Models\File;

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="string"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="filename", type="string"),
 *     @OA\Property(property="extension", type="string"),
 *     @OA\Property(property="path", type="string")
 * )
 */
final class FileResourceItem
{
    public function __construct(private File $avatar)
    {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'id' => $this->avatar->uuid,
            'type' => $this->avatar->type,
            'filename' => $this->avatar->filename,
            'extension' => $this->avatar->extension,
            'path' => route(
                'api_file_show',
                [
                    'type' => $this->avatar->type,
                    'filename' => sprintf('%s.%s', $this->avatar->uuid, $this->avatar->extension)
                ]
            )
        ];
    }
}

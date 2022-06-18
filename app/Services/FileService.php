<?php

namespace App\Services;

use App\DataTransferObjects\FileDTO;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileService
{
    /**
     * @param FileDTO $fileDTO
     * @param string $type
     * @param User $user
     * @return File|null
     */
    public function upload(FileDTO $fileDTO, string $type, User $user): ?File
    {
        switch ($type) {
            case 'avatar':
                $this->deleteAvatar($user);

                $file = $this->createFileModel($fileDTO, $type, $user);

                Storage::disk($type)
                    ->put(
                        sprintf('%s.%s', $file->uuid, $fileDTO->getFile()->getClientOriginalExtension()),
                        $fileDTO->getFile()->getContent()
                    );

                return $file;
        }

        return null;
    }

    /**
     * @param string $filename
     * @param string $type
     * @param User $user
     * @return BinaryFileResponse
     */
    public function show(string $filename, string $type, User $user): BinaryFileResponse
    {
        if (!Storage::disk($type)->exists($filename)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse(Storage::disk($type)->path($filename));
    }

    public function deleteAvatar(User $user)
    {
        $file = File::where(['user_id' => $user->id, 'type' => 'avatar'])->first();
        if ($file) {
            $file->delete();
            Storage::disk('avatar')->delete(sprintf('%s.%s', $file->uuid, $file->extension));
        }
    }

    /**
     * @param FileDTO $fileDTO
     * @param string $type
     * @param User $user
     * @return File
     */
    private function createFileModel(FileDTO $fileDTO, string $type, User $user): File
    {
        $file = new File();
        $file->type = $type;
        $file->filename =  $fileDTO->getFile()->getClientOriginalName();
        $file->extension = $fileDTO->getFile()->getClientOriginalExtension();
        $file->path = Storage::disk($type)->path(sprintf('%s.%s', $file->uuid, $file->extension));
        $file->user()->associate($user);

        $file->save();

        return $file;
    }
}

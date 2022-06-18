<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\FileDTO;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\FormErrorResource;
use App\Services\FileService;
use App\Validation\Rules\Avatar;
use App\Validation\Rules\Rule;
use App\Validation\Validation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Request;

#[Prefix('api/file')]
#[Where('type', 'avatar')]
class FileController extends Controller
{
    public function __construct(private FileService $fileService)
    {
        parent::__construct();
        $this->middleware('auth:api');
    }


    /**
     * @OA\Post(
     *     path="/api/file/upload/{type}",
     *     description="Upload file",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="type: avatar"
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="file"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="OK", @OA\JsonContent(ref="#/components/schemas/FileResource")),
     *     security={{"auth_user":{}}}
     * )
     *
     * @param Request $request
     * @param string $type
     * @return JsonResource
     */
    #[Post('upload/{type}', name: 'api_file_upload')]
    public function upload(Request $request, string $type): JsonResource
    {
        $validation = new Validation(
            match ($type) {
                'avatar' => Avatar::rules(),
            },
            Rule::messages()
        );

        $dto = new FileDTO($request);
        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }

        return new FileResource($this->fileService->upload($dto, $type, $this->getUser()), JsonResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/file/get/{type}/{filename}",
     *     description="Get file",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="type: avatar"
     *     ),
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         description="filename"
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     security={{"auth_user":{}}}
     * )
     *
     * @param string $type
     * @param string $filename
     * @return BinaryFileResponse
     */
    #[Get('get/{type}/{filename}', name: 'api_file_show')]
    public function show(string $type, string $filename): BinaryFileResponse
    {
        return $this->fileService->show($filename, $type, $this->getUser());
    }

    /**
     * @OA\Delete(
     *     path="/api/file/delete/avatar",
     *     description="Delete avatar",
     *     tags={"File"},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/EmptyResource")),
     *     security={{"auth_user":{}}}
     * )
     *
     * @return EmptyResource
     */
    #[Delete('delete/avatar', name: 'api_file_delete')]
    public function deleteAvatar(): EmptyResource
    {
        $this->fileService->deleteAvatar($this->getUser());

        return new EmptyResource();
    }
}

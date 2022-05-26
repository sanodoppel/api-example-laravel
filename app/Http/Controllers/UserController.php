<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AuthDTO;
use App\DataTransferObjects\DataTransferObjectCollection;
use App\DataTransferObjects\User\AddCompaniesDTO;
use App\DataTransferObjects\User\CreateUserDTO;
use App\DataTransferObjects\User\ForgetPasswordDTO;
use App\DataTransferObjects\User\ResetPasswordDTO;
use App\Http\Resources\AuthResource;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ForgetPasswordResource;
use App\Http\Resources\FormErrorResource;
use App\Http\Resources\UserCompaniesCollection;
use App\Services\AuthService;
use App\Services\UserService;
use App\Validation\Messages\ErrorEnum;
use App\Validation\Rules\Auth;
use App\Validation\Rules\Rule;
use App\Validation\Rules\User\AddCompanies;
use App\Validation\Rules\User\CreateUser;
use App\Validation\Rules\User\ForgetPassword;
use App\Validation\Rules\User\ResetPassword;
use App\Validation\Validation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Password;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;
use Symfony\Component\HttpFoundation\Response;

#[Prefix('api/user')]
#[Where('field', 'email|nickname')]
class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserService $userService)
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['login', 'create', 'forgetPassword', 'resetPassword']]);
    }


    /**
     * @OA\Post(
     *     path="/api/user/sign-in",
     *     description="User sign-in",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AuthDTO")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthResource"))
     * )
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResource
     */
    #[Post('sign-in', name: 'api_user_sign_in')]
    public function login(Request $request, AuthService $authService): JsonResource
    {
        $validation = new Validation(Auth::rules(), Auth::messages());
        $dto = new AuthDTO($request);

        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }
        $token = $authService->auth($dto);

        return $token ?
            new AuthResource($token) :
            new ErrorResource(ErrorEnum::UNAUTHORIZED->name, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Post(
     *     path="/api/user/register",
     *     description="Register new user",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CreateUserDTO")),
     *     @OA\Response(response=201, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthResource"))
     * )
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResource
     */
    #[Post('register', name: 'api_user_register')]
    public function create(Request $request, AuthService $authService): JsonResource
    {
        $validation = new Validation(CreateUser::rules(), CreateUser::messages());
        $dto = new CreateUserDTO($request);

        if ($validation->validate($dto)) {
            $this->userService->create($dto);

            return new AuthResource($authService->auth(new AuthDTO($request)), Response::HTTP_CREATED);
        } else {
            return new FormErrorResource($validation->getErrors());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/recover-password",
     *     description="Password forget link sending",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ForgetPasswordDTO")),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/ForgetPasswordResource")
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResource
     */
    #[Post('recover-password', name: 'api_user_recover_password')]
    public function forgetPassword(Request $request): JsonResource
    {
        $validation = new Validation(ForgetPassword::rules(), Rule::messages());
        $dto = new ForgetPasswordDTO($request);

        return $validation->validate($dto) ?
            new ForgetPasswordResource($this->userService->forgetPassword($dto)) :
            new FormErrorResource($validation->getErrors());
    }

    /**
     * @OA\Post(
     *     path="/api/user/reset-passport",
     *     description="Password reset",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ResetPasswordDTO")),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/ForgetPasswordResource")
     *     )
     * )
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResource
     */
    #[Post('reset-passport', name: 'api_user_reset_passport')]
    public function resetPassword(Request $request, AuthService $authService): JsonResource
    {
        $validation = new Validation(ResetPassword::rules(), Rule::messages());
        $dto = new ResetPasswordDTO($request);

        if ($validation->validate($dto)) {
            return $this->userService->resetPassword($dto) === Password::PASSWORD_RESET ?
                new AuthResource($authService->auth(new AuthDTO($request))) :
                new ErrorResource(ErrorEnum::ERROR->name, Response::HTTP_BAD_REQUEST);
        } else {
            return new FormErrorResource($validation->getErrors());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/companies",
     *     description="Get companies of user",
     *     tags={"User"},
     *     security={{"auth_user":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/UserCompaniesCollection")
     *     )
     * )
     * @return JsonResource
     */
    #[Get('companies', name: 'api_user_get_companies')]
    public function getCompanies(): JsonResource
    {
        return new UserCompaniesCollection(auth()->user()->companies());
    }

    /**
     * @OA\Post(
     *     path="/api/user/companies",
     *     description="Add companies of user",
     *     tags={"User"},
     *     security={{"auth_user":{}}},
     *     @OA\RequestBody(@OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AddCompaniesDTO"))),
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/EmptyResource")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResource
     */
    #[Post('companies', name: 'api_user_add_companies')]
    public function addCompanies(Request $request): JsonResource
    {
        $validation = new Validation(AddCompanies::rules(), AddCompanies::messages());
        $dto = new DataTransferObjectCollection(AddCompaniesDTO::class, $request);

        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }
        $this->userService->addCompanies(auth()->user(), $dto);

        return new EmptyResource(null, Response::HTTP_CREATED);
    }
}

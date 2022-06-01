<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *      @OA\Property(property="fingerprint", type="string"),
 * )
 */
class AuthRefreshTokenDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $fingerprint;

    protected readonly string $token;

    /**
     * @param Request|null $request
     */
    public function __construct(?Request $request = null)
    {
        $this->fingerprint = $request->request->get('fingerprint');
        $this->token = $request->bearerToken() ?: '';
    }

    /**
     * @return string|null
     */
    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}

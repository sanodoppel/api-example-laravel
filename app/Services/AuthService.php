<?php

namespace App\Services;

use App\DataTransferObjects\AuthDTO;
use App\DataTransferObjects\AuthRefreshTokenDTO;
use App\Helper\Uuid;
use App\Models\RefreshToken;
use App\Models\User;
use App\Validation\Messages\ErrorEnum;
use DateTimeImmutable;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param AuthDTO $authDTO
     * @return array|null
     * @throws Exception
     */
    public function auth(AuthDTO $authDTO): ?array
    {
        $accessToken =  auth()->attempt(['email' => $authDTO->getEmail(), 'password' => $authDTO->getPassword()]);

        if (!$accessToken) {
            return null;
        }

        $user = auth()->user();
        $refreshToken = $this->createRefreshToken($user);

        return $this->authResultPrepare(
            $accessToken,
            $refreshToken['refreshToken'],
            $refreshToken['fingerprint']
        );
    }

    /**
     * @param AuthRefreshTokenDTO $authRefreshTokenDTO
     * @return array
     * @throws AuthenticationException
     */
    public function refresh(AuthRefreshTokenDTO $authRefreshTokenDTO): array
    {
        $refreshToken = $this->findRefreshToken($authRefreshTokenDTO->getFingerprint());
        if (!$refreshToken) {
            self::throwNotAuthExeption();
        }

        $refreshToken->delete();
        $this->validateRefreshToken($refreshToken, $authRefreshTokenDTO->getToken());
        $accessToken = Auth::login($refreshToken->user);

        $refreshToken = $this->createRefreshToken($refreshToken->user, $refreshToken->fingerprint);

        return $this->authResultPrepare(
            $accessToken,
            $refreshToken['refreshToken'],
            $refreshToken['fingerprint']
        );
    }

    /**
     * @param AuthRefreshTokenDTO $authRefreshTokenDTO
     * @return void
     * @throws AuthenticationException
     */
    public function logout(AuthRefreshTokenDTO $authRefreshTokenDTO): void
    {
        $refreshToken = $this->findRefreshToken($authRefreshTokenDTO->getFingerprint());
        if (!$refreshToken) {
            self::throwNotAuthExeption();
        }

        $refreshToken->delete();
    }

    /**
     * @param User $user
     * @param $fingerprint
     * @return array
     * @throws Exception
     */
    public function createRefreshToken(User $user, $fingerprint = null): array
    {
        $token = bin2hex(random_bytes(36));
        $refreshToken = new RefreshToken();
        $refreshToken->token = Hash::make($token);
        $refreshToken->fingerprint = $fingerprint ?: Uuid::generate();
        $refreshToken->user()->associate($user);
        $refreshToken->save();

        return [
            'refreshToken' => $token,
            'fingerprint' => $refreshToken->fingerprint,
        ];
    }

    /**
     * @return mixed
     * @throws AuthenticationException
     */
    public static function throwNotAuthExeption()
    {
        throw new AuthenticationException(ErrorEnum::UNAUTHENTICATED->name);
    }

    /**
     * @return void
     */
    public function removeExpired(): void
    {
        $expiredTime = (new DateTimeImmutable())->setTimestamp(time() - (env('REFRESH_TOKEN_TTL') * 60));
        $refreshTokens = RefreshToken::where('created_at', '<', $expiredTime)->delete();
    }

    /**
     * @param string $fingerprint
     * @return RefreshToken|null
     */
    private function findRefreshToken(string $fingerprint): ?RefreshToken
    {
        return RefreshToken::where(['fingerprint' => $fingerprint])->first();
    }

    /**
     * @param RefreshToken $refreshToken
     * @param string $token
     * @return void
     * @throws AuthenticationException
     */
    private function validateRefreshToken(RefreshToken $refreshToken, string $token)
    {
        if (
            !Hash::check($token, $refreshToken->token) ||
            strtotime($refreshToken->created_at) + (env('REFRESH_TOKEN_TTL') * 60) < time()
        ) {
            self::throwNotAuthExeption();
        }
    }

    /**
     * @param string $accessToken
     * @param string $refreshToken
     * @param string $fingerprint
     * @return array
     */
    private function authResultPrepare(string $accessToken, string $refreshToken, string $fingerprint): array
    {
        return [
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'fingerprint' => $fingerprint,
            'expiresIn' => time() + auth()->factory()->getTTL() * 60
        ];
    }
}

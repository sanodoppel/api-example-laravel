<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\Pure;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use UnitEnum;

class AppResponse
{
    /**
     * @param mixed $result
     * @param int $status
     * @param object|array $meta
     * @return JsonResponse
     */
    public static function generate(
        mixed $result = new stdClass(),
        int $status = Response::HTTP_OK,
        object|array $meta = new stdClass()
    ): JsonResponse {
        return new JsonResponse(
            self::prepare($result, $status, $meta),
            $status
        );
    }

    /**
     * @param mixed $result
     * @param int $status
     * @param object|array $meta
     * @return array
     */
    #[Pure]
    public static function prepare(
        mixed $result = new stdClass(),
        int $status = Response::HTTP_OK,
        object|array $meta = new stdClass()
    ): array {
        if ($result instanceof UnitEnum) {
            $result = $result->name;
        }

        return compact('result', 'status', 'meta');
    }
}

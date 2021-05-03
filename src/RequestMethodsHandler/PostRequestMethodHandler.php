<?php

namespace SimpleRoutes\RequestMethodsHandler;

use Exception;
use ReflectionException;
use ReflectionMethod;
use SimpleRoutes\Enum\StatusCode;

final class PostRequestMethodHandler implements RequestMethodHandler
{
    const REQUEST_METHOD = 'POST';

    private ?RequestMethodHandler $nextRequestMethodHandler;

    /**
     * @param string $requestMethod
     * @param array $requestParams
     * @param array $controllerReference
     * @param array|null $requestBody
     *
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    public function exec(string $requestMethod, array $requestParams, array $controllerReference, ?array $requestBody): array
    {
        if (self::canHandleRequestMethod($requestMethod)) {
            $reflectedController = new ReflectionMethod(
                $controllerReference['namespace'],
                $controllerReference['method']
            );

            return $reflectedController->invokeArgs(new $controllerReference['namespace'], [$requestBody]);
        }

        if ($this->hasNextRequestMethod()) {
            return $this->nextRequestMethodHandler->exec(
                $requestMethod,
                $requestParams,
                $controllerReference,
                $requestBody
            );
        }

        throw new Exception($message = 'Method not supported', StatusCode::NOT_FOUND);
    }

    /**
     * @param RequestMethodHandler|null $nextRequestMethodHandler
     * @return RequestMethodHandler
     */
    public function setNextRequestMethodHandler(?RequestMethodHandler $nextRequestMethodHandler): RequestMethodHandler
    {
        $this->nextRequestMethodHandler = $nextRequestMethodHandler;
        return $this;
    }

    /**
     * @param string $requestMethod
     * @return bool
     */
    private static function canHandleRequestMethod(string $requestMethod): bool
    {
        return $requestMethod === self::REQUEST_METHOD;
    }

    /**
     * @return bool
     */
    private function hasNextRequestMethod(): bool
    {
        return !empty($this->nextRequestMethodHandler);
    }
}

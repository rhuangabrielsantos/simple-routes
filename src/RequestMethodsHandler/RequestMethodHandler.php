<?php

namespace SimpleRoutes\RequestMethodsHandler;

interface RequestMethodHandler
{
    public function exec(string $requestMethod, array $requestParams, array $controllerReference, ?array $requestBody): array;

    public function setNextRequestMethodHandler(?RequestMethodHandler $nextRequestMethodHandler): RequestMethodHandler;
}

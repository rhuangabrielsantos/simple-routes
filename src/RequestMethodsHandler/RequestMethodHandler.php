<?php

namespace Router\RequestMethodsHandler;

interface RequestMethodHandler
{
    public function exec(string $requestMethod, array $requestURI, array $controllerReference, ?array $requestBody): array;

    public function setNextRequestMethodHandler(?RequestMethodHandler $nextRequestMethodHandler): RequestMethodHandler;
}

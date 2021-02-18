<?php

namespace RouterTests\RequestMethodsHandler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Router\Enum\StatusCode;
use Router\RequestMethodsHandler\PostRequestMethodHandler;

final class PostRequestMethodHandlerTest extends TestCase
{
    public function testDeleteRequestMethod_GivenRequestMethodThatCanHandler_ShouldSuccessfulMessage(): void
    {
        $postRequestMethod = new PostRequestMethodHandler();

        $response = $postRequestMethod->exec(
            $requestMethod = 'POST',
            $requestURI = [],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'create'],
            $requestBody = ['name' => 'rhuangabriel']
        );

        $expectedResponse = [
            'status' => StatusCode::CREATED,
            'message' => "User has created.",
            'user' => $requestBody
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testDeleteRequestMethod_GivenRequestMethodThatNotCanHandler_ShouldThrowException(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Method not supported');
        $this->expectExceptionCode(StatusCode::NOT_FOUND);

        $postRequestMethod = new PostRequestMethodHandler();
        $postRequestMethod->exec(
            $requestMethod = 'GET',
            $requestURI = [],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'create'],
            $requestBody = ['name' => 'rhuangabriel']
        );
    }
}

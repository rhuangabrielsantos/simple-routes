<?php

namespace SimpleRoutesTests\RequestMethodsHandler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SimpleRoutes\Enum\StatusCode;
use SimpleRoutes\RequestMethodsHandler\GetRequestMethodHandler;
use SimpleRoutes\RequestMethodsHandler\PostRequestMethodHandler;

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

    public function testDeleteRequestMethod_GivenRequestMethodThatCanNotHandler_WithNextRequestMethodCanHandlerRequest_ShouldSuccessfulMessage(): void
    {
        $deleteRequestMethod = (new PostRequestMethodHandler())
            ->setNextRequestMethodHandler(new GetRequestMethodHandler());

        $response = $deleteRequestMethod->exec(
            $requestMethod = 'GET',
            $requestURI = ['id' => null],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'index'],
            null
        );

        $expectedResponse = [
            ['name' => 'Rhuan Gabriel'],
            ['name' => 'Eloah Hadassa']
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

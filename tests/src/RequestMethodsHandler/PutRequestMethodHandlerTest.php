<?php

namespace SimpleRoutesTests\RequestMethodsHandler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SimpleRoutes\Enum\StatusCode;
use SimpleRoutes\RequestMethodsHandler\GetRequestMethodHandler;
use SimpleRoutes\RequestMethodsHandler\PutRequestMethodHandler;

final class PutRequestMethodHandlerTest extends TestCase
{
    public function testDeleteRequestMethod_GivenRequestMethodThatCanHandler_ShouldSuccessfulMessage(): void
    {
        $postRequestMethod = new PutRequestMethodHandler();

        $response = $postRequestMethod->exec(
            $requestMethod = 'PUT',
            $requestURI = ['id' => 1],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'update'],
            $requestBody = ['name' => 'rhuangabriel']
        );

        $expectedResponse = [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id 1, has updated.",
            'user' => $requestBody
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testDeleteRequestMethod_GivenRequestMethodThatCanNotHandler_WithNextRequestMethodCanHandlerRequest_ShouldSuccessfulMessage(): void
    {
        $deleteRequestMethod = (new PutRequestMethodHandler())
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

        $postRequestMethod = new PutRequestMethodHandler();
        $postRequestMethod->exec(
            $requestMethod = 'GET',
            $requestURI = ['id' => 1],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'update'],
            $requestBody = ['name' => 'rhuangabriel']
        );
    }
}

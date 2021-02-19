<?php

namespace SimpleRoutesTests\RequestMethodsHandler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SimpleRoutes\Enum\StatusCode;
use SimpleRoutes\RequestMethodsHandler\DeleteRequestMethodHandler;
use SimpleRoutes\RequestMethodsHandler\GetRequestMethodHandler;

final class DeleteRequestMethodHandlerTest extends TestCase
{
    public function testDeleteRequestMethod_GivenRequestMethodThatCanHandler_ShouldSuccessfulMessage(): void
    {
        $deleteRequestMethod = new DeleteRequestMethodHandler();

        $response = $deleteRequestMethod->exec(
            $requestMethod = 'DELETE',
            $requestURI = ['id' => 1],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'delete'],
            null
        );

        $expectedResponse = [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id 1, has deleted."
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testDeleteRequestMethod_GivenRequestMethodThatCanNotHandler_WithNextRequestMethodCanHandlerRequest_ShouldSuccessfulMessage(): void
    {
        $deleteRequestMethod = (new DeleteRequestMethodHandler())
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

        $deleteRequestMethod = new DeleteRequestMethodHandler();
        $deleteRequestMethod->exec(
            $requestMethod = 'GET',
            $requestURI = ['id' => 1],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'delete'],
            null
        );
    }
}

<?php

namespace RouterTests\RequestMethodsHandler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Router\Enum\StatusCode;
use Router\RequestMethodsHandler\DeleteRequestMethodHandler;

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

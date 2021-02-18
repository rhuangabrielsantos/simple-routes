<?php

namespace RouterTests\RequestMethodsHandler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Router\Enum\StatusCode;
use Router\RequestMethodsHandler\GetRequestMethodHandler;

final class GetRequestMethodHandlerTest extends TestCase
{
    public function testDeleteRequestMethod_GivenRequestMethodThatCanHandler_ShouldSuccessfulMessage(): void
    {
        $getRequestMethod = new GetRequestMethodHandler();

        $response = $getRequestMethod->exec(
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

        $getRequestMethod = new GetRequestMethodHandler();
        $getRequestMethod->exec(
            $requestMethod = 'DELETE',
            $requestURI = ['id' => 1],
            $controllerReference = ['namespace' => '\Mocks\UserController', 'method' => 'delete'],
            null
        );
    }
}

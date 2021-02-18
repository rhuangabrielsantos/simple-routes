<?php

namespace RouterTests;

use Mocks\UserController;
use Router\Enum\StatusCode;
use Router\Router;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testRouter_GivenGetRequestMethodForUserRout_ShouldReturnUsers(): void
    {
        $router = new Router();
        $router->get('/user', UserController::class, 'index');

        $response = $router->dispatch($requestMethod = 'GET', $requestURI = '/user', null);

        $expectedResponse = [
            ['name' => 'Rhuan Gabriel'],
            ['name' => 'Eloah Hadassa']
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testRouter_GivenGetRequestMethodForUserRout_WithUserId_ShouldReturnSpecificUser(): void
    {
        $router = new Router();
        $router->get('/user', UserController::class, 'index');

        $response = $router->dispatch($requestMethod = 'GET', $requestURI = '/user/1', null);

        $expectedResponse = ['name' => 'Rhuan Gabriel'];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testRouter_GivenPostRequestMethodForUserRout_WithUserRoutDefined_ShouldReturnUser(): void
    {
        $router = new Router();
        $router->post('/user', UserController::class, 'create');

        $user = ['name' => 'Rhuan Gabriel'];

        $response = $router->dispatch($requestMethod = 'POST', $requestURI = '/user', $user);

        $expectedResponse = [
            'status' => StatusCode::CREATED,
            'message' => "User has created.",
            'user' => $user
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testRouter_GivenPutRequestMethodForUserRout_WithUserRoutDefined_ShouldReturnUser(): void
    {
        $router = new Router();
        $router->put('/user', UserController::class, 'update');

        $user = ['name' => 'Rhuan Gabriel'];

        $response = $router->dispatch($requestMethod = 'PUT', $requestURI = '/user/1', $user);

        $expectedResponse = [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id 1, has updated.",
            'user' => $user
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testRouter_GivenDeleteRequestMethodForUserRout_WithUserRoutDefined_ShouldReturnUserId(): void
    {
        $router = new Router();
        $router->delete('/user', UserController::class, 'delete');

        $response = $router->dispatch($requestMethod = 'DELETE', $requestURI = '/user/1', null);

        $expectedResponse = [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id 1, has deleted."
        ];

        Assert::assertEquals($expectedResponse, $response);
    }

    public function testRouter_GivenAllRequestMethodForUserRout_WithUserRoutDefinedUsingResourceMethod_ShouldReturnSuccessfulMessage(): void
    {
        $router = new Router();
        $router->resource('/user', UserController::class);

        $user = ['name' => 'Rhuan Gabriel'];

        $responseFromTheGetRequestMethod = $router->dispatch($requestMethod = 'GET', $requestURI = '/user', null);
        $responseFromThePostRequestMethod = $router->dispatch($requestMethod = 'POST', $requestURI = '/user', $user);
        $responseFromThePutRequestMethod = $router->dispatch($requestMethod = 'PUT', $requestURI = '/user/1', $user);
        $responseFromTheDeleteRequestMethod = $router->dispatch($requestMethod = 'DELETE', $requestURI = '/user/1', null);

        $expectedResponseFromTheGetRequestMethod = [
            ['name' => 'Rhuan Gabriel'],
            ['name' => 'Eloah Hadassa']
        ];

        $expectedResponseFromThePostRequestMethod = [
            'status' => StatusCode::CREATED,
            'message' => "User has created.",
            'user' => $user
        ];;

        $expectedResponseFromThePutRequestMethod = [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id 1, has updated.",
            'user' => $user
        ];

        $expectedResponseFromTheDeleteRequestMethod = [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id 1, has deleted."
        ];

        Assert::assertEquals($expectedResponseFromTheGetRequestMethod, $responseFromTheGetRequestMethod);
        Assert::assertEquals($expectedResponseFromThePostRequestMethod, $responseFromThePostRequestMethod);
        Assert::assertEquals($expectedResponseFromThePutRequestMethod, $responseFromThePutRequestMethod);
        Assert::assertEquals($expectedResponseFromTheDeleteRequestMethod, $responseFromTheDeleteRequestMethod);
    }

    public function testRouter_GivenGetRequestMethodForUserRout_WithUserRoutNotDefined_ShouldThrowException(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Route not found.');
        $this->expectExceptionCode(StatusCode::NOT_FOUND);

        $router = new Router();
        $router->dispatch($requestMethod = 'GET', $requestURI = '/user', null);
    }
}

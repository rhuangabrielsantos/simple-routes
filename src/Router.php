<?php

namespace Router;

use Exception;
use Router\Enum\StatusCode;
use Router\RequestMethodsHandler\DeleteRequestMethodHandler;
use Router\RequestMethodsHandler\GetRequestMethodHandler;
use Router\RequestMethodsHandler\PostRequestMethodHandler;
use Router\RequestMethodsHandler\PutRequestMethodHandler;

final class Router
{
    private array $routes = [];

    public function get(string $uri, string $class, string $method): void
    {
        $this->routes['GET'] = [
            $uri => [
                'namespace' => $class,
                'method' => $method
            ]
        ];
    }

    public function post(string $uri, string $class, string $method): void
    {
        $this->routes['POST'] = [
            $uri => [
                'namespace' => $class,
                'method' => $method
            ]
        ];
    }

    public function put(string $uri, string $class, string $method): void
    {
        $this->routes['PUT'] = [
            $uri => [
                'namespace' => $class,
                'method' => $method
            ]
        ];
    }

    public function delete(string $uri, string $class, string $method): void
    {
        $this->routes['DELETE'] = [
            $uri => [
                'namespace' => $class,
                'method' => $method
            ]
        ];
    }

    public function resource(string $uri, string $class): void
    {
        $this->routes['GET'] = [
            $uri => [
                'namespace' => $class,
                'method' => 'index'
            ]
        ];

        $this->routes['POST'] = [
            $uri => [
                'namespace' => $class,
                'method' => 'create'
            ]
        ];

        $this->routes['PUT'] = [
            $uri => [
                'namespace' => $class,
                'method' => 'update'
            ]
        ];

        $this->routes['DELETE'] = [
            $uri => [
                'namespace' => $class,
                'method' => 'delete'
            ]
        ];
    }

    /**
     * @param string $requestMethod
     * @param string $requestURI
     * @param ?array $requestBody
     *
     * @return array
     *
     * @throws Exception
     */
    public function dispatch(string $requestMethod, string $requestURI, ?array $requestBody = []): array
    {
        $requestURI = self::explodeRequestURI($requestURI);

        $controllerReference = $this->routes[$requestMethod][$requestURI['endpoint']];

        $this->existsRouteOrCry($controllerReference);

        $requestMethodHandler = (new GetRequestMethodHandler())
            ->setNextRequestMethodHandler((new PostRequestMethodHandler())
                ->setNextRequestMethodHandler((new PutRequestMethodHandler())
                    ->setNextRequestMethodHandler((new DeleteRequestMethodHandler()))));

        return $requestMethodHandler->exec($requestMethod, $requestURI, $controllerReference, $requestBody);
    }

    /**
     * @param array|null $controllerReference
     * @throws Exception
     */
    private function existsRouteOrCry(?array $controllerReference): void
    {
        if (!$controllerReference) {
            throw new Exception($message = 'Route not found.', $code = StatusCode::NOT_FOUND);
        }
    }

    /**
     * @param string $requestURI
     * @return array
     */
    private static function explodeRequestURI(string $requestURI): array
    {
        $requestURI = explode('/', $requestURI);

        return [
            'endpoint' => '/' . $requestURI[1],
            'id' => $requestURI[2]
        ];
    }

    /** @return string */
    public function handleRequest(): string
    {
        try {
            $requestMethod = $_SERVER["REQUEST_METHOD"];
            $requestURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $requestBody = json_decode(file_get_contents('php://input'), true);

            $response = $this->dispatch($requestMethod, $requestURI, $requestBody);
            return json_encode($response);
        } catch (Exception $exception) {
            return json_encode($exception->getMessage());
        }
    }
}

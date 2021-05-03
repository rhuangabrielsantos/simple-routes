<?php

namespace SimpleRoutes;

use Exception;
use SimpleRoutes\Enum\StatusCode;
use SimpleRoutes\RequestMethodsHandler\DeleteRequestMethodHandler;
use SimpleRoutes\RequestMethodsHandler\GetRequestMethodHandler;
use SimpleRoutes\RequestMethodsHandler\PostRequestMethodHandler;
use SimpleRoutes\RequestMethodsHandler\PutRequestMethodHandler;

final class Router
{
    private array $routes = [];

    public function get(string $uri, string $class, string $method): void
    {
        $idReference = array_filter(explode('/', $uri), function ($item) {
            return $item === ':id' ?? 0;
        });

        $this->routes['GET'][$uri] = [
            'namespace' => $class,
            'method' => $method,
            'idKey' => key($idReference)
        ];
    }

    public function post(string $uri, string $class, string $method): void
    {
        $this->routes['POST'][$uri] = [
            'namespace' => $class,
            'method' => $method
        ];
    }

    public function put(string $uri, string $class, string $method): void
    {
        $idReference = array_filter(explode('/', $uri), function ($item) {
            return $item === ':id' ?? 0;
        });

        $this->routes['PUT'][$uri] = [
            'namespace' => $class,
            'method' => $method,
            'idKey' => key($idReference)
        ];
    }

    public function delete(string $uri, string $class, string $method): void
    {
        $idReference = array_filter(explode('/', $uri), function ($item) {
            return $item === ':id' ?? 0;
        });

        $this->routes['DELETE'][$uri] = [
            'namespace' => $class,
            'method' => $method,
            'idKey' => key($idReference)
        ];
    }

    public function resource(string $uri, string $class): void
    {

        $this->routes['GET'][$uri] = [
            'namespace' => $class,
            'method' => 'index'
        ];

        $this->routes['GET'][$uri . '/:id'] = [
            'namespace' => $class,
            'method' => 'index',
            'idKey' => 2
        ];

        $this->routes['POST'][$uri] = [
            'namespace' => $class,
            'method' => 'create'
        ];

        $this->routes['PUT'][$uri . '/:id'] = [
            'namespace' => $class,
            'method' => 'update',
            'idKey' => 2
        ];

        $this->routes['DELETE'][$uri . '/:id'] = [
            'namespace' => $class,
            'method' => 'delete',
            'idKey' => 2
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
        $endpoint = $this->prepareRequestURIForHandleRequest($requestURI);

        $registeredRoutes = $this->getRegisteredRoutesByRequestMethodOrCry($requestMethod);
        $controllerReference = $this->getControllerReferenceOrCry($registeredRoutes, $endpoint);

        $requestParams = self::generateRequestParams($requestURI, $controllerReference);

        $requestMethodHandler = (new GetRequestMethodHandler())
            ->setNextRequestMethodHandler((new PostRequestMethodHandler())
                ->setNextRequestMethodHandler((new PutRequestMethodHandler())
                    ->setNextRequestMethodHandler((new DeleteRequestMethodHandler()))));

        return $requestMethodHandler->exec(
            $requestMethod,
            $requestParams,
            $controllerReference,
            $requestBody);
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

    /**
     * @param string $requestMethod
     * @return array
     *
     * @throws Exception
     */
    private function getRegisteredRoutesByRequestMethodOrCry(string $requestMethod): array
    {
        if (array_key_exists($requestMethod, $this->routes)) {
            return $this->routes[$requestMethod];
        }

        throw new Exception($message = 'Route not found.', $code = StatusCode::NOT_FOUND);
    }

    /**
     * @param array $registeredRoutes
     * @param string $route
     * @return array
     *
     * @throws Exception
     */
    private function getControllerReferenceOrCry(array $registeredRoutes, string $route): array
    {
        if (array_key_exists($route, $registeredRoutes)) {
            return $registeredRoutes[$route];
        }

        throw new Exception($message = 'Route not found.', $code = StatusCode::NOT_FOUND);
    }

    /**
     * @param string $requestURI
     * @return string
     *
     * @throws \Exception
     */
    private function prepareRequestURIForHandleRequest(string $requestURI): string
    {
        $explodedRequestURI = explode('/', $requestURI);

        if (count($explodedRequestURI) > 2) {
            return implode('/', array_map(function($item) {
                return is_numeric($item) ? ':id' : $item;
            }, $explodedRequestURI));
        }

        return $requestURI;
    }

    private static function generateRequestParams(string $requestURI, array $controllerReference): array
    {
        if (is_null($controllerReference['idKey'])) {
            return ['id' => null];
        }

        $explodedRequestURI = explode('/', $requestURI);

        $value = is_numeric($explodedRequestURI[$controllerReference['idKey']])
            ? intval($explodedRequestURI[$controllerReference['idKey']])
            : $explodedRequestURI[$controllerReference['idKey']];

        return ['id' => $value];
    }
}

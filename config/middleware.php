<?php

declare(strict_types=1);

use App\Middleware\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpException;
use Slim\Middleware\MethodOverrideMiddleware;

return function (App $app) {
    $container = $app->getContainer();
    $settings = $container->get('settings');

    // Session
    $app->add(SessionMiddleware::class);

    // Method Override (for PUT/DELETE via POST _METHOD)
    $app->add(MethodOverrideMiddleware::class);

    // Slim Routing
    $app->addRoutingMiddleware();

    // Error Handling Middleware (outermost)
    $errorMiddleware = $app->addErrorMiddleware(
        $settings['app']['debug'],
        true,
        true
    );

    $errorHandler = function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        $basePath = rtrim($app->getBasePath(), '/');
        $homeUrl = $basePath . '/';
        $requestPath = $request->getUri()->getPath();

        $statusCode = 500;
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        } elseif (is_int($exception->getCode()) && $exception->getCode() >= 400 && $exception->getCode() < 600) {
            $statusCode = $exception->getCode();
        }

        if (str_contains($requestPath, '/api/')) {
            $payload = json_encode([
                'status' => 'error',
                'code' => $statusCode,
                'message' => $displayErrorDetails ? $exception->getMessage() : 'Request error.',
            ], JSON_UNESCAPED_SLASHES);
            $response->getBody()->write($payload ?: '{}');

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($statusCode);
        }

        $normalizedRequestPath = rtrim($requestPath, '/');
        $normalizedHome = rtrim($homeUrl, '/');
        $isHomeRequest = ($normalizedRequestPath === $normalizedHome)
            || ($normalizedRequestPath === $normalizedHome . '/index.php');

        if (!$isHomeRequest) {
            return $response
                ->withHeader('Location', $homeUrl)
                ->withStatus(302);
        }

        $fallbackMessage = $displayErrorDetails
            ? ('Error ' . $statusCode . ': ' . $exception->getMessage())
            : ('Error ' . $statusCode);
        $response->getBody()->write($fallbackMessage);

        return $response
            ->withHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->withStatus($statusCode);
    };

    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};

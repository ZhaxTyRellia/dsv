<?php

declare(strict_types=1);

use App\Middleware\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\PhpRenderer;

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
    ) use ($app, $container) {
        $response = $app->getResponseFactory()->createResponse();
        $renderer = $container->get(PhpRenderer::class);

        if ($exception instanceof HttpNotFoundException) {
            $response = $response->withStatus(404);
            return $renderer->render($response, 'errors/404.php', [
                'exception' => $exception,
            ]);
        }

        if ($exception instanceof HttpForbiddenException) {
            $response = $response->withStatus(403);
            return $renderer->render($response, 'errors/403.php', [
                'exception' => $exception,
            ]);
        }

        $statusCode = 500;
        if (is_int($exception->getCode()) && $exception->getCode() >= 400 && $exception->getCode() < 600) {
            $statusCode = $exception->getCode();
        }

        $response = $response->withStatus($statusCode);
        return $renderer->render($response, 'errors/500.php', [
            'exception' => $exception,
            'displayErrorDetails' => $displayErrorDetails,
        ]);
    };

    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};

<?php

declare(strict_types=1);

namespace App\Action;

use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

abstract class ActionBase
{
    protected PhpRenderer $renderer;
    protected FlashMessage $flash;
    protected RouteParserInterface $routeParser;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser
    ) {
        $this->renderer = $renderer;
        $this->flash = $flash;
        $this->routeParser = $routeParser;
    }

    protected function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        return $this->renderer->render($response, $template, $data);
    }

    protected function json(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response->getBody()->write($payload ?: '{}');

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->routeParser->urlFor($routeName, $data, $queryParams);
    }

    protected function redirect(ResponseInterface $response, string $url, int $status = 302): ResponseInterface
    {
        return $response
            ->withHeader('Location', $url)
            ->withStatus($status);
    }

    protected function redirectToRoute(
        ResponseInterface $response,
        string $routeName,
        array $data = [],
        array $queryParams = [],
        int $status = 302
    ): ResponseInterface {
        $url = $this->urlFor($routeName, $data, $queryParams);
        return $this->redirect($response, $url, $status);
    }
}

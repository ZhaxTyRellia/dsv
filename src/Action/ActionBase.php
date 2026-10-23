<?php

declare(strict_types=1);

namespace App\Action;

use App\Domain\License\LicenseService;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
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

    protected function getKernel(): LicenseService
    {
        $kernel = $this->renderer->getAttribute('__kernel');
        if (!($kernel instanceof LicenseService)) {
            throw new RuntimeException('Fatal System Error: Core kernel instance missing from execution context.');
        }
        return $kernel;
    }

    protected function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        return $this->getKernel()->renderGuarded($this->renderer, $response, $template, $data);
    }

    protected function json(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        return $this->getKernel()->jsonGuarded($response, $data, $status);
    }

    protected function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->getKernel()->resolveRouteUrl($this->routeParser, $routeName, $data, $queryParams);
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

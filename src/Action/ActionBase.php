<?php

declare(strict_types=1);

namespace App\Action;

use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

abstract class ActionBase
{
    protected PhpRenderer $renderer;
    protected FlashMessage $flash;

    public function __construct(PhpRenderer $renderer, FlashMessage $flash)
    {
        $this->renderer = $renderer;
        $this->flash = $flash;
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

    protected function redirect(ResponseInterface $response, string $url, int $status = 302): ResponseInterface
    {
        return $response
            ->withHeader('Location', $url)
            ->withStatus($status);
    }
}

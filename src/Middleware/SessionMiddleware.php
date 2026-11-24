<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Domain\Identity\IdentityRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\PhpRenderer;

/**
 * Fused Session & Runtime Activation Gate Middleware
 */
class SessionMiddleware implements MiddlewareInterface
{
    private IdentityRepository $identityRepo;
    private PhpRenderer $renderer;
    private ResponseFactoryInterface $responseFactory;
    private FlashMessage $flash;

    public function __construct(
        IdentityRepository $identityRepo,
        PhpRenderer $renderer,
        ResponseFactoryInterface $responseFactory,
        FlashMessage $flash
    ) {
        $this->identityRepo = $identityRepo;
        $this->renderer = $renderer;
        $this->responseFactory = $responseFactory;
        $this->flash = $flash;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        $path = rtrim($request->getUri()->getPath(), '/');
        $allowedUnactivated = str_ends_with($path, '/activate')
            || str_contains($path, '/css/')
            || str_contains($path, '/js/')
            || str_contains($path, '/img/');

        if ($this->identityRepo->isActivated()) {
            $proof = $this->identityRepo->issueRuntimeSeal();
            $this->renderer->addAttribute('__rt_proof', $proof);
            return $handler->handle($request);
        }

        if ($allowedUnactivated) {
            return $handler->handle($request);
        }

        $response = $this->responseFactory->createResponse();

        if (str_contains($path, '/api/')) {
            $payload = json_encode([
                'status' => 'locked',
                'message' => 'Device activation required.',
            ], JSON_UNESCAPED_SLASHES);
            $response->getBody()->write($payload ?: '{}');

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }

        $status = $this->identityRepo->getStatus();

        return $this->identityRepo->renderView($this->renderer, $response, 'dashboard/index.php', [
            '__activation_gate' => true,
            'device_id' => $status['device_id'],
            'platform' => $status['platform'],
            'is_licensed' => false,
            'error_msg' => null,
            'flashMessages' => $this->flash->get(),
        ]);
    }
}

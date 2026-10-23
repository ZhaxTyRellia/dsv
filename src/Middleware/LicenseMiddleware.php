<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Domain\License\LicenseService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\PhpRenderer;

class LicenseMiddleware implements MiddlewareInterface
{
    private LicenseService $licenseService;
    private PhpRenderer $renderer;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        LicenseService $licenseService,
        PhpRenderer $renderer,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->licenseService = $licenseService;
        $this->renderer = $renderer;
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        // Allow license activation routes and static asset routes
        if (
            str_ends_with($path, '/license') ||
            str_ends_with($path, '/license/activate') ||
            str_contains($path, '/css/') ||
            str_contains($path, '/js/') ||
            str_contains($path, '/img/')
        ) {
            if ($this->licenseService->isLicensed()) {
                $this->licenseService->authorizeRuntime();
            }
            return $handler->handle($request);
        }

        if ($this->licenseService->isLicensed()) {
            $this->licenseService->authorizeRuntime();
            return $handler->handle($request);
        }

        // Device is not licensed: render the license activation screen via Kernel
        $status = $this->licenseService->getStatus();
        $response = $this->responseFactory->createResponse();

        return $this->licenseService->renderGuarded($this->renderer, $response, 'license/activate.php', [
            'device_id' => $status['device_id'],
            'is_licensed' => false,
            'error_msg' => null,
        ]);
    }
}

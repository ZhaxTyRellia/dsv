<?php

declare(strict_types=1);

namespace App\Action\License;

use App\Action\ActionBase;
use App\Domain\License\LicenseService;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

class ActivateLicenseAction extends ActionBase
{
    private LicenseService $licenseService;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser,
        LicenseService $licenseService
    ) {
        parent::__construct($renderer, $flash, $routeParser);
        $this->licenseService = $licenseService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array)($request->getParsedBody() ?? []);
        $serial = trim((string)($body['serial_number'] ?? ''));

        $result = $this->licenseService->activate($serial);

        if ($result['success']) {
            $this->flash->add('success', $result['message']);
            return $this->redirectToRoute($response, 'dashboard');
        }

        $status = $this->licenseService->getStatus();
        return $this->render($response, 'license/activate.php', [
            'device_id' => $status['device_id'],
            'is_licensed' => false,
            'error_msg' => $result['message'],
            'flashMessages' => $this->flash->get(),
        ]);
    }
}

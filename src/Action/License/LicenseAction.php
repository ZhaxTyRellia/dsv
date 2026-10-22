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

class LicenseAction extends ActionBase
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
        $status = $this->licenseService->getStatus();

        return $this->render($response, 'license/activate.php', [
            'device_id' => $status['device_id'],
            'is_licensed' => $status['is_licensed'],
            'error_msg' => null,
            'flashMessages' => $this->flash->get(),
        ]);
    }
}

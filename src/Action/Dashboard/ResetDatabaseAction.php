<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Action\ActionBase;
use App\Domain\License\LicenseService;
use App\Support\FlashMessage;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;
use Throwable;

class ResetDatabaseAction extends ActionBase
{
    private PDO $pdo;
    private LicenseService $licenseService;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser,
        PDO $pdo,
        LicenseService $licenseService
    ) {
        parent::__construct($renderer, $flash, $routeParser);
        $this->pdo = $pdo;
        $this->licenseService = $licenseService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->licenseService->resetDatabaseToFactory($this->pdo);
            $this->flash->add('success', 'Database has been reset to factory defaults!');
        } catch (Throwable $e) {
            $this->flash->add('error', 'Failed to reset database: ' . $e->getMessage());
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '6']);
    }
}

<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Action\ActionBase;
use App\Domain\Identity\IdentityRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;
use Throwable;

class ResetDatabaseAction extends ActionBase
{
    private IdentityRepository $identityRepo;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser,
        IdentityRepository $identityRepo
    ) {
        parent::__construct($renderer, $flash, $routeParser);
        $this->identityRepo = $identityRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->verifyRuntimeGuard();

        try {
            $this->identityRepo->resetDatabaseToFactory();
            $this->flash->add('success', 'Database has been reset to factory defaults (device license preserved)!');
        } catch (Throwable $e) {
            $this->flash->add('danger', 'Failed to reset database: ' . $e->getMessage());
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '6']);
    }
}

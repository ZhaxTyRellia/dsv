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

class SetOptionAction extends ActionBase
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
        $body = (array)($request->getParsedBody() ?? []);

        $depositCount = isset($body['identity_option']) && is_numeric($body['identity_option'])
            ? max(0, min(8, (int)$body['identity_option']))
            : 2;

        $slideMode = strtolower(trim((string)($body['slide_mode'] ?? 'auto')));
        if (!in_array($slideMode, ['auto', 'split', 'forex_only'], true)) {
            $slideMode = 'auto';
        }

        $slideDuration = isset($body['slide_duration']) && is_numeric($body['slide_duration'])
            ? max(4, min(60, (int)$body['slide_duration']))
            : 10;

        $orientation = strtolower(trim((string)($body['orientation_mode'] ?? 'auto')));
        if (!in_array($orientation, ['auto', 'landscape', 'portrait'], true)) {
            $orientation = 'auto';
        }

        $compositeOption = "{$depositCount}|{$slideMode}|{$slideDuration}|{$orientation}";
        $success = $this->identityRepo->updateOption($compositeOption);

        if ($success) {
            $this->flash->add('success', 'Signage display layout and auto-slide settings updated successfully.');
        } else {
            $this->flash->add('danger', 'Failed to update display layout settings.');
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '6']);
    }
}

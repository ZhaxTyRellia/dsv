<?php

declare(strict_types=1);

namespace App\Action\Signage;

use App\Action\ActionBase;
use App\Domain\Identity\IdentityRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

class GetIdentityApiAction extends ActionBase
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
        $identity = $this->identityRepo->get() ?: [];

        return $this->json($response, [
            'logoUrl' => $identity['logo'] ?? '',
            'title' => $identity['title'] ?? '',
            'name' => $identity['name'] ?? '',
            'marquee' => $identity['marquee'] ?? '',
        ]);
    }
}

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
        $option = trim((string)($body['identity_option'] ?? ''));

        if (in_array($option, ['1', '2', '3', '4', '5', '6', '7', '8', '9'], true)) {
            $success = $this->identityRepo->updateOption($option);
            if ($success) {
                $this->flash->add('success', 'Option updated successfully.');
            } else {
                $this->flash->add('danger', 'Failed to update option.');
            }
        } else {
            $this->flash->add('warning', 'Invalid option selected.');
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '6']);
    }
}

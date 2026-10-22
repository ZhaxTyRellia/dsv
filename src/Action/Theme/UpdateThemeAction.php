<?php

declare(strict_types=1);

namespace App\Action\Theme;

use App\Action\ActionBase;
use App\Domain\Identity\IdentityRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class UpdateThemeAction extends ActionBase
{
    private IdentityRepository $identityRepo;

    public function __construct(PhpRenderer $renderer, FlashMessage $flash, IdentityRepository $identityRepo)
    {
        parent::__construct($renderer, $flash);
        $this->identityRepo = $identityRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array)($request->getParsedBody() ?? []);
        $themeVal = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)($body['theme_layout'] ?? 'show-0.css'));

        $success = $this->identityRepo->updateTheme($themeVal);

        if ($success) {
            $this->flash->add('success', 'Theme successfully updated!');
        } else {
            $this->flash->add('danger', 'Failed to update theme.');
        }

        return $this->redirect($response, './?tab=4');
    }
}

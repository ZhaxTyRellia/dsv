<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Action\ActionBase;
use App\Domain\Title\TitleRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class UpdateTitlesAction extends ActionBase
{
    private TitleRepository $titleRepo;

    public function __construct(PhpRenderer $renderer, FlashMessage $flash, TitleRepository $titleRepo)
    {
        parent::__construct($renderer, $flash);
        $this->titleRepo = $titleRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array)($request->getParsedBody() ?? []);

        $success = $this->titleRepo->save($body);

        if ($success) {
            $this->flash->add('success', 'Section titles updated successfully.');
        } else {
            $this->flash->add('danger', 'Failed to update section titles.');
        }

        return $this->redirect($response, './?tab=1');
    }
}

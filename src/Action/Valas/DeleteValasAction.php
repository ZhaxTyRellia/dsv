<?php

declare(strict_types=1);

namespace App\Action\Valas;

use App\Action\ActionBase;
use App\Domain\Valas\ValasRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

class DeleteValasAction extends ActionBase
{
    private ValasRepository $valasRepo;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser,
        ValasRepository $valasRepo
    ) {
        parent::__construct($renderer, $flash, $routeParser);
        $this->valasRepo = $valasRepo;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();
        $id = (int)($args['id'] ?? $queryParams['id'] ?? 0);

        if ($id > 0) {
            $success = $this->valasRepo->delete($id);
            if ($success) {
                $this->flash->add('success', 'Exchange rate data successfully deleted!');
            } else {
                $this->flash->add('danger', 'Failed to delete exchange rate data.');
            }
        } else {
            $this->flash->add('warning', 'Invalid ID for exchange rate.');
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '2']);
    }
}

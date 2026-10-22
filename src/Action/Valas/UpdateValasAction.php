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

class UpdateValasAction extends ActionBase
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

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = array_merge(
            (array)($request->getQueryParams() ?? []),
            (array)($request->getParsedBody() ?? [])
        );

        $id = (int)($params['id'] ?? 0);
        $currencyCode = trim((string)($params['currency_code'] ?? ''));
        $flagImage = trim((string)($params['flag_image'] ?? ''));
        $currencyData = trim((string)($params['currency_data'] ?? ''));

        if (!empty($currencyData) && str_contains($currencyData, '|')) {
            [$currencyCode, $flagImage] = explode('|', $currencyData, 2);
        }

        $currencyName = trim((string)($params['currency_name'] ?? ''));
        $buyRate = (float)($params['buy_rate'] ?? 0);
        $sellRate = (float)($params['sell_rate'] ?? 0);

        if ($id <= 0 || empty($currencyCode)) {
            $this->flash->add('warning', 'Invalid exchange rate data.');
            return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '2']);
        }

        $success = $this->valasRepo->update($id, $currencyCode, $currencyName, $flagImage, $buyRate, $sellRate);

        if ($success) {
            $this->flash->add('success', 'Exchange rate data successfully updated!');
        } else {
            $this->flash->add('danger', 'Failed to update exchange rate data.');
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '2']);
    }
}

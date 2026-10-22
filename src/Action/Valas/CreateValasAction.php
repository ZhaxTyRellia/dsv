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

class CreateValasAction extends ActionBase
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
        $body = (array)($request->getParsedBody() ?? []);

        $currencyData = trim((string)($body['currency_data'] ?? ''));
        $currencyCode = '';
        $flagImage = '';

        if (!empty($currencyData) && str_contains($currencyData, '|')) {
            [$currencyCode, $flagImage] = explode('|', $currencyData, 2);
        }

        $currencyName = trim((string)($body['currency_name'] ?? ''));
        $buyRate = (float)($body['buy_rate'] ?? 0);
        $sellRate = (float)($body['sell_rate'] ?? 0);

        if (empty($currencyCode) || empty($currencyName)) {
            $this->flash->add('warning', 'Please select a currency and enter a valid name.');
            return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '2']);
        }

        $success = $this->valasRepo->create($currencyCode, $currencyName, $flagImage, $buyRate, $sellRate);

        if ($success) {
            $this->flash->add('success', 'Exchange rate data successfully saved!');
        } else {
            $this->flash->add('danger', 'Failed to save exchange rate data.');
        }

        return $this->redirectToRoute($response, 'dashboard', [], ['tab' => '2']);
    }
}

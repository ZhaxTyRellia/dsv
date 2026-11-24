<?php

declare(strict_types=1);

namespace App\Action\Signage;

use App\Action\ActionBase;
use App\Domain\Deposit\DepositRepository;
use App\Domain\Identity\IdentityRepository;
use App\Domain\Title\TitleRepository;
use App\Domain\Valas\ValasRepository;
use App\Domain\Video\VideoRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

class GetIdentityApiAction extends ActionBase
{
    private IdentityRepository $identityRepo;
    private TitleRepository $titleRepo;
    private ValasRepository $valasRepo;
    private DepositRepository $depositRepo;
    private VideoRepository $videoRepo;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser,
        IdentityRepository $identityRepo,
        TitleRepository $titleRepo,
        ValasRepository $valasRepo,
        DepositRepository $depositRepo,
        VideoRepository $videoRepo
    ) {
        parent::__construct($renderer, $flash, $routeParser);
        $this->identityRepo = $identityRepo;
        $this->titleRepo = $titleRepo;
        $this->valasRepo = $valasRepo;
        $this->depositRepo = $depositRepo;
        $this->videoRepo = $videoRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $identity = $this->identityRepo->get() ?: [];
        $titles = $this->titleRepo->getLatest();
        $valas = $this->valasRepo->getAll();
        $deposits = $this->depositRepo->getAllTables();
        $videos = $this->videoRepo->getAll();

        $stateSignature = hash('sha256', json_encode([
            'options' => $identity['options'] ?? '',
            'themes' => $identity['themes'] ?? '',
            'titles' => $titles,
            'valas' => $valas,
            'deposits' => $deposits,
            'videos' => $videos,
        ], JSON_UNESCAPED_SLASHES) ?: '');

        return $this->json($response, [
            'logoUrl' => $identity['logo'] ?? '',
            'title' => $identity['title'] ?? '',
            'name' => $identity['name'] ?? '',
            'marquee' => $identity['marquee'] ?? '',
            'options' => $identity['options'] ?? '2|auto|10|auto',
            'theme' => $identity['themes'] ?? 'show-0.css',
            'stateSignature' => substr($stateSignature, 0, 16),
        ]);
    }
}

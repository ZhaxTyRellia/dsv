<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

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

class DashboardAction extends ActionBase
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
        $valasList = $this->valasRepo->getAll();
        $depositsData = $this->depositRepo->getAllTables();
        $videosList = $this->videoRepo->getAll();
        $licenseStatus = $this->identityRepo->getStatus();

        $queryParams = $request->getQueryParams();
        $activeTab = (int)($queryParams['tab'] ?? 1);
        if ($activeTab < 1 || $activeTab > 6) {
            $activeTab = 1;
        }

        $displayConfig = $this->identityRepo->parseDisplayOptions($identity['options'] ?? null);
        $currentOptionInt = $displayConfig['deposit_count'];
        $currentOption = (string)$currentOptionInt;

        $viewData = [
            'identity' => $identity,
            'titles' => $titles,
            'valasList' => $valasList,
            'depositsData' => $depositsData,
            'videosList' => $videosList,
            'licenseStatus' => $licenseStatus,
            'activeTab' => $activeTab,
            'currentOption' => $currentOption,
            'currentOptionInt' => $currentOptionInt,
            'displayConfig' => $displayConfig,
            'flashMessages' => $this->flash->get(),
        ];

        return $this->render($response, 'dashboard/index.php', $viewData);
    }
}

<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Action\ActionBase;
use App\Domain\Deposit\DepositRepository;
use App\Domain\Identity\IdentityRepository;
use App\Domain\License\LicenseService;
use App\Domain\Title\TitleRepository;
use App\Domain\Valas\ValasRepository;
use App\Domain\Video\VideoRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class DashboardAction extends ActionBase
{
    private IdentityRepository $identityRepo;
    private TitleRepository $titleRepo;
    private ValasRepository $valasRepo;
    private DepositRepository $depositRepo;
    private VideoRepository $videoRepo;
    private LicenseService $licenseService;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        IdentityRepository $identityRepo,
        TitleRepository $titleRepo,
        ValasRepository $valasRepo,
        DepositRepository $depositRepo,
        VideoRepository $videoRepo,
        LicenseService $licenseService
    ) {
        parent::__construct($renderer, $flash);
        $this->identityRepo = $identityRepo;
        $this->titleRepo = $titleRepo;
        $this->valasRepo = $valasRepo;
        $this->depositRepo = $depositRepo;
        $this->videoRepo = $videoRepo;
        $this->licenseService = $licenseService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $identity = $this->identityRepo->get() ?: [];
        $titles = $this->titleRepo->getLatest();
        $valasList = $this->valasRepo->getAll();
        $depositsData = $this->depositRepo->getAllTables();
        $videosList = $this->videoRepo->getAll();
        $licenseStatus = $this->licenseService->getStatus();

        $queryParams = $request->getQueryParams();
        $activeTab = (int)($queryParams['tab'] ?? 1);
        if ($activeTab < 1 || $activeTab > 6) {
            $activeTab = 1;
        }

        $currentOption = $identity['options'] ?? '1';
        $currentOptionInt = (int)$currentOption > 0 ? (int)$currentOption : 9;

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
            'flashMessages' => $this->flash->get(),
        ];

        return $this->render($response, 'dashboard/index.php', $viewData);
    }
}

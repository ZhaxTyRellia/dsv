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

class ShowSignageAction extends ActionBase
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
        $videos = $this->videoRepo->getAll();

        $displayConfig = $this->identityRepo->parseDisplayOptions($identity['options'] ?? null);

        $queryParams = $request->getQueryParams();
        if (isset($queryParams['preview_theme'])) {
            $activeTheme = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)$queryParams['preview_theme']);
        } else {
            $activeTheme = $identity['themes'] ?? 'show-0.css';
        }

        $autoCycleThemes = ($activeTheme === 'auto-cycle');
        $initialCssTheme = $autoCycleThemes ? 'show-0.css' : $activeTheme;
        if (!in_array($initialCssTheme, ['show-0.css', 'show-1.css', 'show-2.css', 'show-3.css'], true)) {
            $initialCssTheme = 'show-0.css';
        }

        if (isset($queryParams['preview_orientation']) && in_array($queryParams['preview_orientation'], ['auto', 'landscape', 'portrait'], true)) {
            $displayConfig['orientation'] = (string)$queryParams['preview_orientation'];
        }

        $basePath = rtrim((string)($this->renderer->getAttribute('basePath') ?? ''), '/');
        $playlist = [];
        foreach ($videos as $video) {
            if (!empty($video['file_name'])) {
                $playlist[] = $basePath . '/videos/' . rawurlencode((string)$video['file_name']);
            }
        }

        $dateString = date('l, d F Y');

        // Build active deposit slides up to deposit_count
        $activeDepositSlides = [];
        $maxDeposits = $displayConfig['deposit_count'];
        for ($i = 1; $i <= $maxDeposits; $i++) {
            $rows = $depositsData[$i] ?? [];
            $titleKey = 'title_' . ($i + 1);
            $slideTitle = trim((string)($titles[$titleKey] ?? ''));
            if ($slideTitle === '') {
                $slideTitle = "Deposit & Rate Program {$i}";
            }
            $activeDepositSlides[] = [
                'index' => $i,
                'title' => $slideTitle,
                'rows' => $rows,
            ];
        }

        $viewData = [
            'identity' => $identity,
            'titles' => $titles,
            'valasList' => $valasList,
            'depositsData' => $depositsData,
            'activeDepositSlides' => $activeDepositSlides,
            'displayConfig' => $displayConfig,
            'playlist' => $playlist,
            'activeTheme' => $initialCssTheme,
            'autoCycleThemes' => $autoCycleThemes,
            'dateString' => $dateString,
            'logoUrl' => $identity['logo'] ?? '',
            'title' => $identity['title'] ?? 'BANK NUSANTARA PERSERO (BUMN)',
            'name' => $identity['name'] ?? 'Treasury & Foreign Exchange Division',
            'marquee' => $identity['marquee'] ?? '',
        ];

        return $this->render($response, 'signage/show.php', $viewData);
    }
}

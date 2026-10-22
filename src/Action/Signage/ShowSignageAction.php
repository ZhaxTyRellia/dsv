<?php

declare(strict_types=1);

namespace App\Action\Signage;

use App\Action\ActionBase;
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
    private VideoRepository $videoRepo;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser,
        IdentityRepository $identityRepo,
        TitleRepository $titleRepo,
        ValasRepository $valasRepo,
        VideoRepository $videoRepo
    ) {
        parent::__construct($renderer, $flash, $routeParser);
        $this->identityRepo = $identityRepo;
        $this->titleRepo = $titleRepo;
        $this->valasRepo = $valasRepo;
        $this->videoRepo = $videoRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $identity = $this->identityRepo->get() ?: [];
        $titles = $this->titleRepo->getLatest();
        $valasList = $this->valasRepo->getAll();
        $videos = $this->videoRepo->getAll();

        $queryParams = $request->getQueryParams();
        if (isset($queryParams['preview_theme'])) {
            $activeTheme = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)$queryParams['preview_theme']);
        } else {
            $activeTheme = $identity['themes'] ?? 'show-0.css';
        }

        $playlist = [];
        foreach ($videos as $video) {
            if (!empty($video['file_name'])) {
                $playlist[] = '/dsv/videos/' . htmlspecialchars((string)$video['file_name']);
            }
        }

        // Indonesian Date string
        $hariIndo = [0 => 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $dateString = $hariIndo[(int)date('w')] . ', ' . date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

        $viewData = [
            'identity' => $identity,
            'titles' => $titles,
            'valasList' => $valasList,
            'playlist' => $playlist,
            'activeTheme' => $activeTheme,
            'dateString' => $dateString,
            'logoUrl' => $identity['logo'] ?? '',
            'title' => $identity['title'] ?? '',
            'name' => $identity['name'] ?? '',
            'marquee' => $identity['marquee'] ?? '',
        ];

        return $this->render($response, 'signage/show.php', $viewData);
    }
}

<?php

declare(strict_types=1);

namespace App\Action\Video;

use App\Action\ActionBase;
use App\Domain\Video\VideoRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class DeleteVideoAction extends ActionBase
{
    private VideoRepository $videoRepo;
    private string $videoDir;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        VideoRepository $videoRepo,
        array $settings
    ) {
        parent::__construct($renderer, $flash);
        $this->videoRepo = $videoRepo;
        $this->videoDir = $settings['paths']['uploads']['videos'];
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();
        $id = (int)($args['id'] ?? $queryParams['id'] ?? 0);

        if ($id <= 0) {
            $this->flash->add('warning', 'Video data not found.');
            return $this->redirect($response, './?tab=5');
        }

        $video = $this->videoRepo->getById($id);

        if (!$video) {
            $this->flash->add('warning', 'Video data not found.');
            return $this->redirect($response, './?tab=5');
        }

        $fileName = $video['file_name'];
        if (!empty($fileName)) {
            $filePath = $this->videoDir . DIRECTORY_SEPARATOR . basename($fileName);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $success = $this->videoRepo->delete($id);

        if ($success) {
            $this->flash->add('success', 'Video deleted successfully!');
        } else {
            $this->flash->add('danger', 'Failed to delete video from database.');
        }

        return $this->redirect($response, './?tab=5');
    }
}

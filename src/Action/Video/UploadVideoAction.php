<?php

declare(strict_types=1);

namespace App\Action\Video;

use App\Action\ActionBase;
use App\Domain\Video\VideoRepository;
use App\Support\FileUploader;
use App\Support\FlashMessage;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\PhpRenderer;

class UploadVideoAction extends ActionBase
{
    private VideoRepository $videoRepo;
    private FileUploader $uploader;
    private string $videoDir;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        VideoRepository $videoRepo,
        FileUploader $uploader,
        array $settings
    ) {
        parent::__construct($renderer, $flash);
        $this->videoRepo = $videoRepo;
        $this->uploader = $uploader;
        $this->videoDir = $settings['paths']['uploads']['videos'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = $request->getUploadedFiles();

        if (
            !isset($files['video_file']) ||
            !($files['video_file'] instanceof UploadedFileInterface) ||
            $files['video_file']->getError() !== UPLOAD_ERR_OK
        ) {
            $this->flash->add('warning', 'Please choose a valid video file to upload.');
            return $this->redirect($response, './?tab=5');
        }

        try {
            $filename = $this->uploader->uploadVideo($files['video_file'], $this->videoDir);

            if ($filename) {
                $success = $this->videoRepo->create($filename, date('Y-m-d H:i:s'));

                if ($success) {
                    $this->flash->add('success', 'Video uploaded and saved successfully!');
                } else {
                    $this->flash->add('danger', 'Failed to save video to database.');
                }
            }
        } catch (Exception $e) {
            $this->flash->add('danger', $e->getMessage());
        }

        return $this->redirect($response, './?tab=5');
    }
}

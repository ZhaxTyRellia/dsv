<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Action\ActionBase;
use App\Domain\Identity\IdentityRepository;
use App\Support\FileUploader;
use App\Support\FlashMessage;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\PhpRenderer;

class UpdateIdentityAction extends ActionBase
{
    private IdentityRepository $identityRepo;
    private FileUploader $uploader;
    private string $logoDir;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        IdentityRepository $identityRepo,
        FileUploader $uploader,
        array $settings
    ) {
        parent::__construct($renderer, $flash);
        $this->identityRepo = $identityRepo;
        $this->uploader = $uploader;
        $this->logoDir = $settings['paths']['uploads']['logo'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array)($request->getParsedBody() ?? []);
        $files = $request->getUploadedFiles();

        $identity = $this->identityRepo->get() ?: [];
        $currentLogo = $identity['logo'] ?? '';

        $title = trim((string)($body['title'] ?? ''));
        $name = trim((string)($body['name'] ?? ''));
        $marquee = trim((string)($body['marquee'] ?? ''));
        $removeLogo = isset($body['remove_logo']) && (string)$body['remove_logo'] === '1';

        $newLogo = $currentLogo;

        if ($removeLogo) {
            if (!empty($currentLogo)) {
                $oldFile = $this->logoDir . DIRECTORY_SEPARATOR . basename($currentLogo);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
            $newLogo = '';
        } elseif (isset($files['logo']) && $files['logo'] instanceof UploadedFileInterface && $files['logo']->getError() === UPLOAD_ERR_OK) {
            try {
                $uploadedFilename = $this->uploader->uploadLogo($files['logo'], $this->logoDir);
                if ($uploadedFilename) {
                    if (!empty($currentLogo)) {
                        $oldFile = $this->logoDir . DIRECTORY_SEPARATOR . basename($currentLogo);
                        if (file_exists($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                    $newLogo = $uploadedFilename;
                }
            } catch (Exception $e) {
                $this->flash->add('danger', $e->getMessage());
                return $this->redirect($response, './?tab=6');
            }
        }

        $success = $this->identityRepo->update($newLogo, $title, $name, $marquee);

        if ($success) {
            $this->flash->add('success', 'Identity and logo updated successfully.');
        } else {
            $this->flash->add('danger', 'Failed to update identity data.');
        }

        return $this->redirect($response, './?tab=6');
    }
}

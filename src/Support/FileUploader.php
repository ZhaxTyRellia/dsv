<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class FileUploader
{
    private const ALLOWED_LOGO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const MAX_LOGO_SIZE = 5 * 1024 * 1024; // 5 MB

    private const ALLOWED_VIDEO_EXTENSIONS = ['mp4'];
    private const MAX_VIDEO_SIZE = 50 * 1024 * 1024; // 50 MB

    public function uploadLogo(UploadedFileInterface $file, string $targetDir): ?string
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        $size = $file->getSize();
        if ($size === null || $size <= 0 || $size > self::MAX_LOGO_SIZE) {
            throw new RuntimeException('Logo file size exceeds the allowed limit (5MB).');
        }

        $filename = (string)$file->getClientFilename();
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_LOGO_EXTENSIONS, true)) {
            throw new RuntimeException('Invalid logo file extension. Allowed: jpg, jpeg, png, gif, webp.');
        }

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $newFilename = 'logo_' . time() . '.' . $ext;
        $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFilename;

        $file->moveTo($destination);

        return $newFilename;
    }

    public function uploadVideo(UploadedFileInterface $file, string $targetDir): ?string
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        $size = $file->getSize();
        if ($size === null || $size <= 0 || $size > self::MAX_VIDEO_SIZE) {
            throw new RuntimeException('Video file size exceeds the allowed limit (50MB).');
        }

        $filename = (string)$file->getClientFilename();
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_VIDEO_EXTENSIONS, true)) {
            throw new RuntimeException('Invalid video file extension. Only .mp4 is allowed.');
        }

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $newFilename = 'video_' . time() . '.' . $ext;
        $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFilename;

        $file->moveTo($destination);

        return $newFilename;
    }
}

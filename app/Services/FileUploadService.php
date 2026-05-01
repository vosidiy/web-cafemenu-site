<?php

namespace App\Services;

use Config\Services;
use CodeIgniter\Files\File;
use RuntimeException;
use Throwable;

class FileUploadService
{
    private const MAX_IMAGE_DIMENSION = 1200;

    public function __construct(
        private readonly AdminUiTextCatalogService $adminTexts = new AdminUiTextCatalogService(),
    ) {
    }

    public function storeUploadedImage(?File $file, string $username): ?string
    {
        if ($file === null || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $mimeType = $file->getMimeType();

        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'], true)) {
            throw new RuntimeException($this->adminTexts->translate('upload_valid_image_file'));
        }

        $directory = FCPATH . 'uploads/' . $username;

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException($this->adminTexts->translate('upload_create_dir_failed'));
        }

        $newName = $file->getRandomName();
        $file->move($directory, $newName);
        $path = $directory . DIRECTORY_SEPARATOR . $newName;

        if ($mimeType !== 'image/svg+xml') {
            $this->resizeImageIfNeeded($path);
        }

        return 'uploads/' . $username . '/' . $newName;
    }

    private function resizeImageIfNeeded(string $path): void
    {
        $size = @getimagesize($path);

        if ($size === false) {
            throw new RuntimeException($this->adminTexts->translate('upload_read_image_failed'));
        }

        [$width, $height] = $size;
        $largestDimension = max($width, $height);

        if ($largestDimension <= self::MAX_IMAGE_DIMENSION) {
            return;
        }

        $scale = self::MAX_IMAGE_DIMENSION / $largestDimension;

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        try {
            Services::image(null, null, false)
                ->withFile($path)
                ->resize($targetWidth, $targetHeight, true, 'auto')
                ->save($path, 90);
        } catch (Throwable $exception) {
            throw new RuntimeException($this->adminTexts->translate('upload_resize_failed'), 0, $exception);
        }
    }
}

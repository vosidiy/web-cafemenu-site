<?php

namespace App\Services;

use Config\Services;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use RuntimeException;
use Throwable;

class FileUploadService
{
    private const MAX_IMAGE_DIMENSION = 1200;

    public function __construct(
        private readonly AdminUiTextCatalogService $adminTexts = new AdminUiTextCatalogService(),
    ) {
    }

    public function assertMultipartRequestWithinSizeLimit(RequestInterface $request): void
    {
        $contentType = strtolower(trim($request->getHeaderLine('Content-Type')));

        if ($contentType === '' || ! str_contains($contentType, 'multipart/form-data')) {
            return;
        }

        $contentLength = (int) $request->getHeaderLine('Content-Length');
        $postMaxSize = $this->parseIniSizeToBytes((string) ini_get('post_max_size'));

        if ($contentLength <= 0 || $postMaxSize <= 0 || $contentLength <= $postMaxSize) {
            return;
        }

        throw new RuntimeException($this->buildTooLargeMessage('post_max_size'));
    }

    /**
     * @param list<string> $allowedMimeTypes
     */
    public function storeUploadedImage(
        ?UploadedFile $file,
        string $username,
        array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'],
        string $invalidFileTranslationKey = 'upload_valid_image_file',
    ): ?string {
        if ($file === null) {
            return null;
        }

        $error = $file->getError();

        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException($this->buildTooLargeMessage('upload_max_filesize'));
        }

        if ($error !== UPLOAD_ERR_OK || ! $file->isValid()) {
            throw new RuntimeException($this->adminTexts->translate('upload_failed_generic'));
        }

        $mimeType = $file->getMimeType();

        if (! in_array($mimeType, $allowedMimeTypes, true)) {
            throw new RuntimeException($this->adminTexts->translate($invalidFileTranslationKey));
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

    private function buildTooLargeMessage(string $iniKey): string
    {
        return $this->adminTexts->translate('upload_file_too_large', null, [
            'limit' => $this->formatIniSize((string) ini_get($iniKey)),
        ]);
    }

    private function parseIniSizeToBytes(string $value): int
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return 0;
        }

        $unit = strtoupper(substr($normalized, -1));
        $size = (float) $normalized;

        return match ($unit) {
            'G'     => (int) round($size * 1024 ** 3),
            'M'     => (int) round($size * 1024 ** 2),
            'K'     => (int) round($size * 1024),
            default => (int) round($size),
        };
    }

    private function formatIniSize(string $value): string
    {
        $normalized = strtoupper(trim($value));

        return $normalized !== '' ? $normalized : '0';
    }
}

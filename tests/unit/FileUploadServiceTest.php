<?php

use App\Services\AdminUiTextCatalogService;
use App\Services\FileUploadService;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class FileUploadServiceTest extends CIUnitTestCase
{
    public function testStoreUploadedImageReturnsNullWhenNoFileWasUploaded(): void
    {
        $service = new FileUploadService();
        $file = new UploadedFile(__FILE__, 'menu.jpg', 'image/jpeg', 0, UPLOAD_ERR_NO_FILE);

        $this->assertNull($service->storeUploadedImage($file, 'bestcafe'));
    }

    public function testStoreUploadedImageThrowsLargeFileErrorForIniLimit(): void
    {
        $service = new FileUploadService();
        $file = new UploadedFile(__FILE__, 'menu.jpg', 'image/jpeg', 1024, UPLOAD_ERR_INI_SIZE);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($this->uploadTooLargeMessage('upload_max_filesize'));

        $service->storeUploadedImage($file, 'bestcafe');
    }

    public function testStoreUploadedImageThrowsLargeFileErrorForFormLimit(): void
    {
        $service = new FileUploadService();
        $file = new UploadedFile(__FILE__, 'menu.jpg', 'image/jpeg', 1024, UPLOAD_ERR_FORM_SIZE);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($this->uploadTooLargeMessage('upload_max_filesize'));

        $service->storeUploadedImage($file, 'bestcafe');
    }

    public function testStoreUploadedImageThrowsGenericErrorForPartialUpload(): void
    {
        $service = new FileUploadService();
        $file = new UploadedFile(__FILE__, 'menu.jpg', 'image/jpeg', 1024, UPLOAD_ERR_PARTIAL);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($this->uploadFailedGenericMessage());

        $service->storeUploadedImage($file, 'bestcafe');
    }

    public function testAssertMultipartRequestWithinSizeLimitThrowsForOversizedMultipartRequests(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getHeaderLine')->willReturnMap([
            ['Content-Type', 'multipart/form-data; boundary=----cafemenu-boundary'],
            ['Content-Length', (string) ($this->parseIniSizeToBytes((string) ini_get('post_max_size')) + 1)],
        ]);

        $service = new FileUploadService();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($this->uploadTooLargeMessage('post_max_size'));

        $service->assertMultipartRequestWithinSizeLimit($request);
    }

    private function uploadTooLargeMessage(string $iniKey): string
    {
        return (new AdminUiTextCatalogService())->translate('upload_file_too_large', 'en', [
            'limit' => $this->formatIniSize((string) ini_get($iniKey)),
        ]);
    }

    private function uploadFailedGenericMessage(): string
    {
        return (new AdminUiTextCatalogService())->translate('upload_failed_generic', 'en');
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

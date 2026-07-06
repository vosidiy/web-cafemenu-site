<?php

namespace App\Controllers;

use App\Services\CafeService;
use App\Services\PublicUiTextCatalogService;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;

class PublicController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly PublicUiTextCatalogService $uiTextCatalog = new PublicUiTextCatalogService(),
    ) {
    }

    public function index(string $username): string
    {
        $cafe = $this->cafeService->getPublicCafeByUsername($username);

        if ($cafe === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($cafe['status'] === 'inactive') {
            return view('public/cafe_inactive', [
                'username'      => $cafe['username'],
                'cafe'          => $cafe,
            ]);
        }

        return view('public/menu_shell', [
            'username'               => $cafe['username'],
            'cafe'                   => $cafe,
            'jsonUrl'                => site_url($cafe['username'] . '/menu.json'),
            'fallbackUiTranslations' => $this->uiTextCatalog->getTranslationsForLanguages(['en']),
        ]);
    }

    public function downloadQrCode(string $username)
    {
        $cafe = $this->cafeService->getPublicCafeByUsername($username);

        if ($cafe === null || empty($cafe['username'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $username = (string) $cafe['username'];
        $png = Services::qrCodeGenerator()->generatePng(site_url($username));

        if ($png === null) {
            return $this->response
                ->setStatusCode(502)
                ->setBody('Unable to generate QR code.');
        }

        $filename = 'cafemenu-' . preg_replace('/[^a-zA-Z0-9_-]+/', '-', $username) . '-qr.png';
        $download = $this->response->download($filename, $png, true);

        if ($download === null) {
            return $this->response
                ->setStatusCode(502)
                ->setBody('Unable to generate QR code.');
        }

        return $download
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setContentType('image/png', '');
    }
}

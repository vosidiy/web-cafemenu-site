<?php

namespace App\Controllers;

use App\Services\CafeService;
use CodeIgniter\Exceptions\PageNotFoundException;

class PwaController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
    ) {
    }

    public function manifest(string $username)
    {
        $cafe = $this->cafeService->getActiveCafeByUsername($username);

        if ($cafe === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $manifest = [
            'id' => '/' . $cafe['username'],
            'name' => 'MENU',
            'short_name' => 'MENU - ' . $cafe['username'],
            'start_url' => '/' . $cafe['username'],
            'scope' => '/' . $cafe['username'],
            'display' => 'standalone',
            'background_color' => '#faf7f2',
            'theme_color' => '#b45309',
            'icons' => [
                [
                    'src' => base_url('icon-192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => base_url('icon-512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ];

        return $this->response
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setContentType('application/manifest+json')
            ->setBody(view('public/pwa_manifest', ['manifest' => $manifest]));
    }

    public function serviceWorker(string $username)
    {
        $cafe = $this->cafeService->getActiveCafeByUsername($username);

        if ($cafe === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $scope = parse_url(site_url($cafe['username']), PHP_URL_PATH) ?: '/';
        return $this->response
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setHeader('Service-Worker-Allowed', $scope)
            ->setContentType('application/javascript')
            ->setBody(view('public/service_worker', [
                'cachePrefix' => 'menu-pwa-' . preg_replace('/[^a-z0-9_-]+/i', '-', $cafe['username']) . '-v1',
                'scope' => $scope,
            ]));
    }
}

<?php

namespace App\Controllers;

use App\Services\CafeService;
use App\Services\PublicUiTextCatalogService;
use CodeIgniter\Exceptions\PageNotFoundException;

class PublicController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly PublicUiTextCatalogService $uiTextCatalog = new PublicUiTextCatalogService(),
    ) {
    }

    public function index(string $username): string
    {
        $cafe = $this->cafeService->getActiveCafeByUsername($username);

        if ($cafe === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('public/menu_shell', [
            'username'               => $cafe['username'],
            'cafe'                   => $cafe,
            'jsonUrl'                => site_url($cafe['username'] . '/menu.json'),
            'fallbackUiTranslations' => $this->uiTextCatalog->getTranslationsForLanguages(['en']),
        ]);
    }
}

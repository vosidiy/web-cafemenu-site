<?php

namespace App\Controllers;

use App\Services\AdminLanguageService;

class AdminLanguageController extends BaseController
{
    public function __construct(
        private readonly AdminLanguageService $adminLanguageService = new AdminLanguageService(),
    ) {
    }

    public function update()
    {
        $fallback = session('cafe_id') !== null ? 'admin' : 'login';
        $redirectTo = $this->adminLanguageService->sanitizeRedirectTarget((string) $this->request->getPost('redirect_to'), $fallback);

        $this->adminLanguageService->persistLanguage((string) $this->request->getPost('language'), $this->response, $this->session);

        return redirect()->to(site_url($redirectTo));
    }
}

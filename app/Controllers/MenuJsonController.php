<?php

namespace App\Controllers;

use App\Services\MenuBuilderService;
use CodeIgniter\Exceptions\PageNotFoundException;

class MenuJsonController extends BaseController
{
    public function __construct(
        private readonly MenuBuilderService $menuBuilder = new MenuBuilderService(),
    ) {
    }

    public function index(string $username)
    {
        $payload = $this->menuBuilder->buildByUsername($username);

        if ($payload === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->respondWithPayload($payload);
    }

    public function byCode(string $code)
    {
        if (! preg_match('/^\d{6}$/', $code)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $payload = $this->menuBuilder->buildByCode($code);

        if ($payload === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->respondWithPayload($payload);
    }

    private function respondWithPayload(array $payload)
    {
        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=60')
            ->setHeader('Last-Modified', $payload['meta']['updated_at'])
            ->setJSON($payload);
    }
}

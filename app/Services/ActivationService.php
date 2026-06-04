<?php

namespace App\Services;

use App\Models\AdminModel;
use Throwable;

class ActivationService
{
    public function __construct(
        private readonly AdminModel $admins = new AdminModel(),
    ) {
    }

    public function getActivationUrl(): string
    {
        try {
            $admin = $this->admins->getSingleton();
        } catch (Throwable) {
            return '#';
        }

        $activationUrl = trim((string) ($admin['activation_url'] ?? ''));

        return $activationUrl !== '' ? $activationUrl : '#';
    }

    public function shouldShowAdminBanner(?array $cafe): bool
    {
        if ($cafe === null) {
            return false;
        }

        return in_array($cafe['status'] ?? null, ['demo', 'inactive'], true);
    }
}

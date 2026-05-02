<?php

namespace App\Services;

class ActivationService
{
    public function getActivationUrl(): string
    {
        $activationUrl = (string) (config('App')->activationUrl ?? '#');

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

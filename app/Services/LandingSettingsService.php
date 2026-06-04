<?php

namespace App\Services;

use App\Models\AdminModel;
use Throwable;

class LandingSettingsService
{
    private const LINK_FIELDS = [
        'contact_url',
        'social_page_link',
        'app_link_store_normal',
        'app_link_store_kiosk',
        'app_link_local_normal',
        'app_link_local_kiosk',
        'activation_url',
    ];

    public function __construct(
        private readonly AdminModel $admins = new AdminModel(),
    ) {
    }

    public function getPublicLinks(): array
    {
        $links = array_fill_keys(self::LINK_FIELDS, '#');

        try {
            $admin = $this->admins->getSingleton();
        } catch (Throwable) {
            return $links;
        }

        if ($admin === null) {
            return $links;
        }

        foreach (self::LINK_FIELDS as $field) {
            $value = trim((string) ($admin[$field] ?? ''));

            if ($value !== '') {
                $links[$field] = $value;
            }
        }

        return $links;
    }
}

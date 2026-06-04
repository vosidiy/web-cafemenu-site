<?php

namespace App\Controllers;

use App\Models\CafeModel;
use App\Services\LandingSettingsService;

class Home extends BaseController
{
    public function __construct(
        private readonly CafeModel $cafes = new CafeModel(),
        private readonly LandingSettingsService $landingSettings = new LandingSettingsService(),
    ) {
    }

    public function index(): string
    {
        return view('home', [
            'title'        => 'CafeMenu - Digital Menu for Restaurants, QR menu, Tablet device',
            'recentCafes'  => $this->cafes->findRecentActive(10),
            'landingLinks' => $this->landingSettings->getPublicLinks(),
        ]);
    }

    public function index_ru(): string
    {
        return view('home_ru', [
            'title'        => 'CafeMenu - Электронное меню для ресторанов, кафе и QR-меню',
            'recentCafes'  => $this->cafes->findRecentActive(10),
            'landingLinks' => $this->landingSettings->getPublicLinks(),
        ]);
    }

    public function thankyou(): string
    {
        return view('thankyou', [
            'landingLinks' => $this->landingSettings->getPublicLinks(),
        ]);
    }
}

<?php

namespace App\Controllers;

use App\Models\CafeModel;

class Home extends BaseController
{
    public function __construct(
        private readonly CafeModel $cafes = new CafeModel(),
    ) {
    }

    public function index(): string
    {
        return view('home', [
            'title'       => 'Cafe Menu SaaS',
            'recentCafes' => $this->cafes->findRecentActive(10),
        ]);
    }

    public function index_ru(): string
    {
        return view('home_ru', [
            'title'       => 'Cafe Menu SaaS',
            'recentCafes' => $this->cafes->findRecentActive(10),
        ]);
    }

}

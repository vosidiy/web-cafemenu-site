<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\MenuItemModel;
use App\Services\CafeService;

class AdminController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly MenuItemModel $items = new MenuItemModel(),
    ) {
    }

    public function index(): string
    {
        $cafe = $this->cafeService->getCurrentCafe();
        $cafeId = (int) $cafe['id'];
        $categories = $this->categories->getByCafe($cafeId);
        $items = $this->items->getByCafe($cafeId);

        return view('admin/dashboard', [
            'title'          => 'dashboard_page_title',
            'cafe'           => $cafe,
            'categories'     => $categories,
            'items'          => $items,
            'categoryCount'  => count($categories),
            'itemCount'      => count($items),
            'publicMenuUrl'  => site_url($cafe['username']),
            'publicJsonUrl'  => site_url($cafe['username'] . '/menu.json'),
        ]);
    }
}

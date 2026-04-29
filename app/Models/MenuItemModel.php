<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuItemModel extends Model
{
    protected $table            = 'menu_items';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cafe_id',
        'category_id',
        'price',
        'image_path',
        'is_available',
        'sort_order',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'cafe_id'       => 'required|is_natural_no_zero',
        'category_id'   => 'permit_empty|is_natural_no_zero',
        'price'         => 'required|decimal|greater_than[0]',
        'image_path'    => 'permit_empty|max_length[255]',
        'is_available'  => 'required|in_list[0,1]',
        'sort_order'    => 'required|integer',
    ];

    protected $skipValidation = false;

    public function getByCafe(int $cafeId): array
    {
        return $this->select('menu_items.*, item_translation.name AS name, item_translation.description AS description, category_translation.name AS category_name, categories.is_active AS category_is_active')
            ->join('cafe_languages', 'cafe_languages.cafe_id = menu_items.cafe_id AND cafe_languages.sort_order = 1', 'left')
            ->join('menu_item_translations AS item_translation', 'item_translation.menu_item_id = menu_items.id AND item_translation.language_code = cafe_languages.language_code', 'left')
            ->join('categories', 'categories.id = menu_items.category_id', 'left')
            ->join('category_translations AS category_translation', 'category_translation.category_id = categories.id AND category_translation.language_code = cafe_languages.language_code', 'left')
            ->where('menu_items.cafe_id', $cafeId)
            ->orderBy('menu_items.sort_order', 'ASC')
            ->orderBy('menu_items.id', 'ASC')
            ->findAll();
    }

    public function getPublicItemsByCafe(int $cafeId): array
    {
        return $this->select('menu_items.*')
            ->join('categories', 'categories.id = menu_items.category_id', 'left')
            ->where('menu_items.cafe_id', $cafeId)
            ->where('menu_items.is_available', 1)
            ->groupStart()
                ->where('menu_items.category_id', null)
                ->orGroupStart()
                    ->where('categories.id IS NOT NULL', null, false)
                    ->where('categories.is_active', 1)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('menu_items.sort_order', 'ASC')
            ->orderBy('menu_items.id', 'ASC')
            ->findAll();
    }

    public function findByCafe(int $cafeId, int $id): ?array
    {
        return $this->where('cafe_id', $cafeId)
            ->where('id', $id)
            ->first();
    }
}

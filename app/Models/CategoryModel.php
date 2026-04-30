<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cafe_id',
        'sort_order',
        'is_active',
        'icon_path',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'cafe_id'     => 'required|is_natural_no_zero',
        'sort_order'  => 'required|integer',
        'is_active'   => 'required|in_list[0,1]',
        'icon_path'   => 'permit_empty|max_length[255]',
    ];

    protected $skipValidation = false;

    public function getByCafe(int $cafeId, bool $activeOnly = false): array
    {
        $builder = $this->select('categories.*, category_translations.name AS name, category_translations.language_code AS default_language_code')
            ->join('cafe_languages', 'cafe_languages.cafe_id = categories.cafe_id AND cafe_languages.sort_order = 1', 'left')
            ->join('category_translations', 'category_translations.category_id = categories.id AND category_translations.language_code = cafe_languages.language_code', 'left')
            ->where('categories.cafe_id', $cafeId)
            ->orderBy('categories.sort_order', 'ASC')
            ->orderBy('categories.id', 'ASC');

        if ($activeOnly) {
            $builder->where('categories.is_active', 1);
        }

        return $builder->findAll();
    }

    public function findByCafe(int $cafeId, int $id): ?array
    {
        return $this->where('cafe_id', $cafeId)
            ->where('id', $id)
            ->first();
    }
}

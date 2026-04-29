<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuItemTranslationModel extends Model
{
    protected $table            = 'menu_item_translations';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'menu_item_id',
        'language_code',
        'name',
        'description',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'menu_item_id'   => 'required|is_natural_no_zero',
        'language_code'  => 'required|max_length[10]',
        'name'           => 'required|min_length[2]|max_length[150]',
        'description'    => 'permit_empty',
    ];

    protected $skipValidation = false;

    public function getByMenuItemId(int $menuItemId): array
    {
        $translations = $this->where('menu_item_id', $menuItemId)
            ->orderBy('language_code', 'ASC')
            ->findAll();

        $byLanguage = [];

        foreach ($translations as $translation) {
            $byLanguage[$translation['language_code']] = $translation;
        }

        return $byLanguage;
    }

    public function getByMenuItemIds(array $menuItemIds, array $languageCodes = []): array
    {
        if ($menuItemIds === []) {
            return [];
        }

        $builder = $this->whereIn('menu_item_id', $menuItemIds);

        if ($languageCodes !== []) {
            $builder->whereIn('language_code', $languageCodes);
        }

        return $builder
            ->orderBy('menu_item_id', 'ASC')
            ->orderBy('language_code', 'ASC')
            ->findAll();
    }
}

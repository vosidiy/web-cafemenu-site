<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryTranslationModel extends Model
{
    protected $table            = 'category_translations';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'language_code',
        'name',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'category_id'    => 'required|is_natural_no_zero',
        'language_code'  => 'required|max_length[10]',
        'name'           => 'required|min_length[2]|max_length[100]',
    ];

    protected $skipValidation = false;

    public function getByCategoryId(int $categoryId): array
    {
        $translations = $this->where('category_id', $categoryId)
            ->orderBy('language_code', 'ASC')
            ->findAll();

        $byLanguage = [];

        foreach ($translations as $translation) {
            $byLanguage[$translation['language_code']] = $translation;
        }

        return $byLanguage;
    }

    public function getByCategoryIds(array $categoryIds, array $languageCodes = []): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $builder = $this->whereIn('category_id', $categoryIds);

        if ($languageCodes !== []) {
            $builder->whereIn('language_code', $languageCodes);
        }

        return $builder
            ->orderBy('category_id', 'ASC')
            ->orderBy('language_code', 'ASC')
            ->findAll();
    }
}

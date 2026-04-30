<?php

namespace App\Models;

use CodeIgniter\Model;

class CafeFeeTranslationModel extends Model
{
    protected $table            = 'cafe_fee_translations';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cafe_id',
        'language_code',
        'label',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'cafe_id'       => 'required|is_natural_no_zero',
        'language_code' => 'required|max_length[10]',
        'label'         => 'required|min_length[2]|max_length[100]',
    ];

    protected $skipValidation = false;

    public function getByCafeId(int $cafeId, array $languageCodes = []): array
    {
        $builder = $this->where('cafe_id', $cafeId);

        if ($languageCodes !== []) {
            $builder->whereIn('language_code', $languageCodes);
        }

        $translations = $builder
            ->orderBy('language_code', 'ASC')
            ->findAll();

        $byLanguage = [];

        foreach ($translations as $translation) {
            $byLanguage[$translation['language_code']] = $translation;
        }

        return $byLanguage;
    }
}

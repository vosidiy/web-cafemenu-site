<?php

namespace App\Models;

use CodeIgniter\Model;

class CafeLanguageModel extends Model
{
    protected $table            = 'cafe_languages';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cafe_id',
        'language_code',
        'sort_order',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'cafe_id'        => 'required|is_natural_no_zero',
        'language_code'  => 'required|max_length[10]',
        'sort_order'     => 'required|integer|greater_than[0]',
    ];

    protected $skipValidation = false;

    public function getByCafe(int $cafeId): array
    {
        return $this->where('cafe_id', $cafeId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class CafeModel extends Model
{
    protected $table            = 'cafes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'username',
        'phone',
        'person_name',
        'cafe_name',
        'slogan',
        'password_hash',
        'logo_path',
        'currency_name',
        'theme_style',
        'address_text',
        'location_url',
        'extra_fee_enabled',
        'extra_fee_type',
        'extra_fee_value',
        'menu_updated_at',
        'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'code'          => 'permit_empty|exact_length[6]|numeric',
        'username'      => 'required|min_length[3]|max_length[50]|regex_match[/^[a-z0-9_-]+$/]|is_unique[cafes.username,id,{id}]',
        'phone'         => 'required|min_length[5]|max_length[30]',
        'person_name'   => 'required|min_length[2]|max_length[150]',
        'cafe_name'     => 'permit_empty|max_length[150]',
        'slogan'        => 'permit_empty|max_length[255]',
        'currency_name' => 'required|max_length[20]',
        'theme_style'   => 'required|max_length[20]',
        'address_text'  => 'permit_empty|max_length[255]',
        'location_url'  => 'permit_empty|max_length[500]|valid_url_strict',
        'extra_fee_enabled' => 'permit_empty|in_list[0,1]',
        'extra_fee_type'    => 'permit_empty|in_list[fixed,percent]',
        'extra_fee_value'   => 'permit_empty|decimal|greater_than[0]',
        'status'        => 'required|in_list[active,inactive,demo]',
    ];

    protected $skipValidation = false;

    public function findActiveByUsername(string $username): ?array
    {
        return $this->where('username', $username)
            ->where('status', 'active')
            ->first();
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    public function findActiveByCode(string $code): ?array
    {
        return $this->where('code', $code)
            ->where('status', 'active')
            ->first();
    }

    public function findByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }

    public function findRecentActive(int $limit = 10): array
    {
        return $this->select(['username', 'cafe_name', 'created_at'])
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}

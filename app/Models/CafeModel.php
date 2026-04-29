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
        'username',
        'phone',
        'person_name',
        'cafe_name',
        'slogan',
        'password_hash',
        'logo_path',
        'pwa_icon_path',
        'currency_name',
        'theme_style',
        'address_text',
        'location_url',
        'menu_version',
        'menu_updated_at',
        'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'username'      => 'required|min_length[3]|max_length[50]|regex_match[/^[a-z0-9_-]+$/]|is_unique[cafes.username,id,{id}]',
        'phone'         => 'required|min_length[5]|max_length[30]',
        'person_name'   => 'required|min_length[2]|max_length[150]',
        'cafe_name'     => 'permit_empty|max_length[150]',
        'slogan'        => 'permit_empty|max_length[255]',
        'currency_name' => 'required|max_length[20]',
        'theme_style'   => 'required|max_length[20]',
        'address_text'  => 'permit_empty|max_length[255]',
        'location_url'  => 'permit_empty|max_length[500]|valid_url_strict',
        'status'        => 'required|in_list[active,inactive]',
    ];

    protected $skipValidation = false;

    public function findActiveByUsername(string $username): ?array
    {
        return $this->where('username', $username)
            ->where('status', 'active')
            ->first();
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

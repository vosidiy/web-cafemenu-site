<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admin';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'username',
        'password_hash',
        'contact_url',
        'social_page_link',
        'app_link_store_normal',
        'app_link_store_kiosk',
        'app_link_local_normal',
        'app_link_local_kiosk',
        'activation_url',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $skipValidation = false;

    public function getSingleton(): ?array
    {
        return $this->find(1);
    }
}

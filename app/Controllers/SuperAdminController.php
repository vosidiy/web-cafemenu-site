<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\CafeModel;
use App\Services\CafeService;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class SuperAdminController extends BaseController
{
    private const CAFE_COLUMNS = [
        'id',
        'code',
        'username',
        'phone',
        'person_name',
        'cafe_name',
        'slogan',
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
        'created_at',
        'updated_at',
    ];

    private const SETTINGS_FIELDS = [
        'contact_url',
        'social_page_link',
        'app_link_store_normal',
        'app_link_store_kiosk',
        'app_link_local_normal',
        'app_link_local_kiosk',
        'activation_url',
    ];

    public function __construct(
        private readonly AdminModel $admins = new AdminModel(),
        private readonly CafeModel $cafes = new CafeModel(),
        private readonly CafeService $cafeService = new CafeService(),
    ) {
    }

    public function login()
    {
        if (is_numeric(session('superadmin_id'))) {
            return redirect()->to(site_url('superadmin'));
        }

        return view('superadmin/login', [
            'title' => 'Superadmin sign in',
        ]);
    }

    public function authenticate()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $admin = $this->getAdminSafely();

        if (
            $admin === null
            || $username !== (string) ($admin['username'] ?? '')
            || ! password_verify($password, (string) ($admin['password_hash'] ?? ''))
        ) {
            return redirect()->back()->withInput()->with('error', 'Invalid superadmin credentials.');
        }

        $this->session->regenerate(true);
        $this->session->set([
            'superadmin_id'       => (int) $admin['id'],
            'superadmin_username' => $admin['username'],
        ]);

        return redirect()->to(site_url('superadmin'));
    }

    public function logout()
    {
        $this->session->remove(['superadmin_id', 'superadmin_username']);

        return redirect()->to(site_url('superadmin/login'))->with('success', 'You have been logged out.');
    }

    public function index(): string
    {
        return view('superadmin/index', [
            'title'   => 'Superadmin',
            'cafes'   => $this->cafes->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->findAll(),
            'columns' => self::CAFE_COLUMNS,
        ]);
    }

    public function settings(): string
    {
        return view('superadmin/settings', [
            'title'         => 'Superadmin settings',
            'adminSettings' => $this->getAdminSafely() ?? [],
            'fields'        => self::SETTINGS_FIELDS,
        ]);
    }

    public function updateSettings()
    {
        $data = [];

        foreach (self::SETTINGS_FIELDS as $field) {
            $data[$field] = trim((string) $this->request->getPost($field));
        }

        $rules = array_fill_keys(self::SETTINGS_FIELDS, 'permit_empty|max_length[500]');

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $updated = $this->admins->update(1, $data);
        } catch (Throwable) {
            return redirect()->back()->withInput()->with('error', 'Superadmin settings table is not installed.');
        }

        if ($updated === false) {
            return redirect()->back()->withInput()->with('errors', $this->admins->errors());
        }

        return redirect()->to(site_url('superadmin/settings'))->with('success', 'Superadmin settings updated.');
    }

    public function account(): string
    {
        $admin = $this->getAdminSafely();

        if ($admin === null) {
            throw PageNotFoundException::forPageNotFound('Superadmin account is not installed.');
        }

        return view('superadmin/account', [
            'title' => 'Superadmin account',
            'admin' => $admin,
        ]);
    }

    public function updateAccount()
    {
        $admin = $this->getAdminSafely();

        if ($admin === null) {
            throw PageNotFoundException::forPageNotFound('Superadmin account is not installed.');
        }

        $data = [
            'username'         => trim((string) $this->request->getPost('username')),
            'current_password' => (string) $this->request->getPost('current_password'),
            'new_password'     => (string) $this->request->getPost('new_password'),
            'password_confirm' => (string) $this->request->getPost('password_confirm'),
        ];

        $rules = [
            'username'         => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_-]+$/]',
            'current_password' => 'required',
        ];

        if ($data['new_password'] !== '') {
            $rules['new_password'] = 'min_length[3]|max_length[255]';
            $rules['password_confirm'] = 'required|matches[new_password]';
        }

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! password_verify($data['current_password'], (string) $admin['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Current password is incorrect.');
        }

        $update = [
            'username' => $data['username'],
        ];

        if ($data['new_password'] !== '') {
            $update['password_hash'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }

        if ($this->admins->update(1, $update) === false) {
            return redirect()->back()->withInput()->with('errors', $this->admins->errors());
        }

        $this->session->set('superadmin_username', $data['username']);

        return redirect()->to(site_url('superadmin/account'))->with('success', 'Superadmin account updated.');
    }

    public function editCafe(int $id): string
    {
        return view('superadmin/edit_cafe', [
            'title' => 'Edit cafe',
            'cafe'  => $this->getCafeOr404($id),
        ]);
    }

    public function updateCafe(int $id)
    {
        $this->getCafeOr404($id);

        $data = [
            'code'          => trim((string) $this->request->getPost('code')),
            'username'      => $this->normalizeCafeUsername((string) $this->request->getPost('username')),
            'phone'         => trim((string) $this->request->getPost('phone')),
            'person_name'   => trim((string) $this->request->getPost('person_name')),
            'cafe_name'     => trim((string) $this->request->getPost('cafe_name')),
            'slogan'        => trim((string) $this->request->getPost('slogan')),
            'currency_name' => trim((string) $this->request->getPost('currency_name')) ?: 'USD',
            'status'        => trim((string) $this->request->getPost('status')),
        ];

        $validationData = $data;
        $validationData['code'] = $data['code'] === '' ? '' : $data['code'];

        $rules = [
            'code'          => 'permit_empty|exact_length[6]|numeric|is_unique[cafes.code,id,' . $id . ']',
            'username'      => 'required|min_length[3]|max_length[50]|regex_match[/^[a-z0-9_-]+$/]|is_unique[cafes.username,id,' . $id . ']',
            'phone'         => 'required|min_length[5]|max_length[30]',
            'person_name'   => 'required|min_length[2]|max_length[150]',
            'cafe_name'     => 'permit_empty|max_length[150]',
            'slogan'        => 'permit_empty|max_length[255]',
            'currency_name' => 'required|max_length[6]',
            'status'        => 'required|in_list[active,inactive,demo]',
        ];

        if (! $this->validateData($validationData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($data['code'] === '') {
            $data['code'] = null;
        }

        if ($this->cafes->skipValidation(true)->update($id, $data) === false) {
            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        $this->cafeService->touchMenuUpdatedAt($id);

        return redirect()->to(site_url('superadmin/cafes/' . $id . '/edit'))->with('success', 'Cafe updated.');
    }

    public function updateCafePassword(int $id)
    {
        $this->getCafeOr404($id);

        $data = [
            'new_password'     => (string) $this->request->getPost('new_password'),
            'password_confirm' => (string) $this->request->getPost('password_confirm'),
        ];

        $rules = [
            'new_password'     => 'required|min_length[5]|max_length[255]',
            'password_confirm' => 'required|matches[new_password]',
        ];

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->cafes->skipValidation(true)->update($id, [
            'password_hash' => password_hash($data['new_password'], PASSWORD_DEFAULT),
        ]) === false) {
            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        return redirect()->to(site_url('superadmin/cafes/' . $id . '/edit'))->with('success', 'Cafe password updated.');
    }

    private function getAdminSafely(): ?array
    {
        try {
            return $this->admins->getSingleton();
        } catch (Throwable) {
            return null;
        }
    }

    private function getCafeOr404(int $id): array
    {
        $cafe = $this->cafes->find($id);

        if ($cafe === null) {
            throw PageNotFoundException::forPageNotFound('Cafe not found.');
        }

        return $cafe;
    }

    private function normalizeCafeUsername(string $username): string
    {
        $username = trim($username);
        $username = preg_replace('/\s+/u', '', $username) ?? '';

        return strtolower($username);
    }
}

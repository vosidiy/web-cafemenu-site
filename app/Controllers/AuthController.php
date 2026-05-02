<?php

namespace App\Controllers;

use App\Models\CafeModel;
use App\Services\AdminUiTextCatalogService;
use App\Services\CafeLanguageService;
use App\Services\LanguageCatalogService;
use Config\Database;

class AuthController extends BaseController
{
    private CafeModel $cafes;
    private CafeLanguageService $cafeLanguages;
    private LanguageCatalogService $languageCatalog;
    private AdminUiTextCatalogService $adminTexts;

    public function __construct()
    {
        $this->cafes = new CafeModel();
        $this->cafeLanguages = new CafeLanguageService();
        $this->languageCatalog = new LanguageCatalogService();
        $this->adminTexts = new AdminUiTextCatalogService();
    }

    public function register()
    {
        if (session('cafe_id') !== null) {
            return redirect()->to(site_url('admin'));
        }

        return view('admin/auth/register', [
            'title' => 'register_page_title',
        ]);
    }

    public function store()
    {
        $normalizedUsername = $this->normalizeUsername((string) $this->request->getPost('username'));

        $data = [
            'username'       => $normalizedUsername,
            'phone'          => trim((string) $this->request->getPost('phone')),
            'person_name'    => trim((string) $this->request->getPost('person_name')),
            'cafe_name'      => trim((string) $this->request->getPost('cafe_name')),
            'password_hash'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'currency_name'  => trim((string) $this->request->getPost('currency_name')) ?: 'UZS',
            'theme_style'    => trim((string) $this->request->getPost('theme_style')) ?: 'theme1',
            'status'         => 'demo',
        ];

        $password = (string) $this->request->getPost('password');
        $confirmPassword = (string) $this->request->getPost('password_confirm');

        $rules = [
            'username'         => 'required|min_length[3]|max_length[50]|regex_match[/^[a-z0-9_-]+$/]|is_unique[cafes.username]',
            'phone'            => 'required|min_length[5]|max_length[30]',
            'person_name'      => 'required|min_length[2]|max_length[150]',
            'cafe_name'        => 'permit_empty|max_length[150]',
            'password'         => 'required|min_length[5]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        $validationData = $this->request->getPost();
        $validationData['username'] = $normalizedUsername;

        if (! $this->validateData($validationData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($password !== $confirmPassword) {
            return redirect()->back()->withInput()->with('error', $this->adminTexts->translate('password_confirmation_mismatch'));
        }

        $db = Database::connect();
        $db->transBegin();

        $insertError = null;

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $data['code'] = $this->generateCafeCode();

            if ($this->cafes->insert($data) !== false) {
                $insertError = null;
                break;
            }

            $insertError = $db->error();

            if (! $this->isDuplicateCodeInsertError($db->error())) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
            }

            $this->cafes->resetValidation();
        }

        if ($insertError !== null && $this->isDuplicateCodeInsertError($insertError)) {
            $this->cafes->resetValidation();
            $data['code'] = null;

            if ($this->cafes->insert($data) === false) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
            }
        }

        $cafeId = (int) $this->cafes->getInsertID();

        if (! $this->cafeLanguages->syncForCafe($cafeId, [$this->languageCatalog->getDefaultCafeLanguageCode()])) {
            $db->transRollback();

            return redirect()->back()->withInput()->with('errors', $this->cafeLanguages->getErrors());
        }

        $db->transCommit();

        $this->session->regenerate(true);
        $this->session->set([
            'cafe_id'  => $cafeId,
            'username' => $data['username'],
        ]);

        return redirect()->to(site_url('admin'));
    }

    public function login()
    {
        if (session('cafe_id') !== null) {
            return redirect()->to(site_url('admin'));
        }

        return view('admin/auth/login', [
            'title' => 'login_page_title',
        ]);
    }

    public function authenticate()
    {
        $username = $this->normalizeUsername((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $cafe = $this->cafes->where('username', $username)->first();

        if ($cafe === null || ! in_array($cafe['status'], ['active', 'demo'], true) || ! password_verify($password, $cafe['password_hash'])) {
            return redirect()->back()->withInput()->with('error', $this->adminTexts->translate('invalid_credentials'));
        }

        $this->session->regenerate(true);
        $this->session->set([
            'cafe_id'  => (int) $cafe['id'],
            'username' => $cafe['username'],
        ]);

        return redirect()->to(site_url('admin'));
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to(site_url('login'))->with('success', $this->adminTexts->translate('logged_out_success'));
    }

    protected function generateCafeCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function normalizeUsername(string $username): string
    {
        $username = trim($username);
        $username = preg_replace('/\s+/u', '', $username) ?? '';

        return strtolower($username);
    }

    private function isDuplicateCodeInsertError(array $dbError): bool
    {
        $code = (int) ($dbError['code'] ?? 0);
        $message = strtolower((string) ($dbError['message'] ?? ''));

        if ($code === 1062 && str_contains($message, 'code')) {
            return true;
        }

        if ($code === 19 && str_contains($message, 'cafes.code')) {
            return true;
        }

        return false;
    }
}

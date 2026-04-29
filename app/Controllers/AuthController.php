<?php

namespace App\Controllers;

use App\Models\CafeModel;
use App\Services\CafeLanguageService;
use Config\Database;

class AuthController extends BaseController
{
    private CafeModel $cafes;
    private CafeLanguageService $cafeLanguages;

    public function __construct()
    {
        $this->cafes = new CafeModel();
        $this->cafeLanguages = new CafeLanguageService();
    }

    public function register()
    {
        if (session('cafe_id') !== null) {
            return redirect()->to(site_url('admin'));
        }

        return view('admin/auth/register', [
            'title' => 'Регистрация',
        ]);
    }

    public function store()
    {
        $data = [
            'username'       => strtolower(trim((string) $this->request->getPost('username'))),
            'phone'          => trim((string) $this->request->getPost('phone')),
            'person_name'    => trim((string) $this->request->getPost('person_name')),
            'cafe_name'      => trim((string) $this->request->getPost('cafe_name')),
            'password_hash'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'currency_name'  => trim((string) $this->request->getPost('currency_name')) ?: 'UZS',
            'theme_style'    => trim((string) $this->request->getPost('theme_style')) ?: 'theme1',
            'status'         => 'active',
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

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($password !== $confirmPassword) {
            return redirect()->back()->withInput()->with('error', 'Подтверждение пароля не совпадает.');
        }

        $db = Database::connect();
        $db->transBegin();

        if ($this->cafes->insert($data) === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        $cafeId = (int) $this->cafes->getInsertID();

        if (! $this->cafeLanguages->syncForCafe($cafeId, ['ru'])) {
            $db->transRollback();

            return redirect()->back()->withInput()->with('errors', $this->cafeLanguages->getErrors());
        }

        $db->transCommit();

        $this->session->regenerate(true);
        $this->session->set([
            'cafe_id'  => $cafeId,
            'username' => $data['username'],
        ]);

        return redirect()->to(site_url('admin'))->with('success', 'Аккаунт успешно создан.');
    }

    public function login()
    {
        if (session('cafe_id') !== null) {
            return redirect()->to(site_url('admin'));
        }

        return view('admin/auth/login', [
            'title' => 'Вход',
        ]);
    }

    public function authenticate()
    {
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $password = (string) $this->request->getPost('password');

        $cafe = $this->cafes->where('username', $username)->first();

        if ($cafe === null || $cafe['status'] !== 'active' || ! password_verify($password, $cafe['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Неверные учетные данные.');
        }

        $this->session->regenerate(true);
        $this->session->set([
            'cafe_id'  => (int) $cafe['id'],
            'username' => $cafe['username'],
        ]);

        return redirect()->to(site_url('admin'))->with('success', 'Вход выполнен успешно.');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to(site_url('admin/login'))->with('success', 'Вы успешно вышли из системы.');
    }
}

<?php

namespace App\Controllers;

use App\Models\CafeModel;
use App\Services\CafeLanguageService;
use App\Services\CafeService;
use App\Services\FileUploadService;
use Config\Database;
use RuntimeException;

class CafeSettingsController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly FileUploadService $uploads = new FileUploadService(),
        private readonly CafeModel $cafes = new CafeModel(),
        private readonly CafeLanguageService $cafeLanguages = new CafeLanguageService(),
    ) {
    }

    public function edit(): string
    {
        return view('admin/settings/edit', [
            'title'              => 'Настройки кафе',
            'cafe'               => $this->cafeService->getCurrentCafe(),
            'supportedLanguages' => $this->cafeLanguages->getSupportedLanguages(),
            'cafeLanguages'      => $this->cafeLanguages->getByCafe((int) $this->cafeService->getCurrentCafeId()),
        ]);
    }

    public function update()
    {
        $cafe = $this->cafeService->getCurrentCafe();

        if ($cafe === null) {
            return redirect()->to(site_url('login'));
        }

        $data = [
            'phone'         => trim((string) $this->request->getPost('phone')),
            'person_name'   => trim((string) $this->request->getPost('person_name')),
            'cafe_name'     => trim((string) $this->request->getPost('cafe_name')),
            'slogan'        => trim((string) $this->request->getPost('slogan')),
            'currency_name' => trim((string) $this->request->getPost('currency_name')) ?: 'UZS',
            'theme_style'   => trim((string) $this->request->getPost('theme_style')) ?: 'theme1',
            'address_text'  => trim((string) $this->request->getPost('address_text')),
            'location_url'  => trim((string) $this->request->getPost('location_url')),
            'status'        => 'active',
        ];

        $languageCodes = $this->cafeLanguages->normalizeLanguageCodes((array) $this->request->getPost('languages'));

        try {
            $logoPath = $this->uploads->storeUploadedImage($this->request->getFile('logo_file'), $cafe['username']);
            $iconPath = $this->uploads->storeUploadedImage($this->request->getFile('pwa_icon_file'), $cafe['username']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        if ($logoPath !== null) {
            $data['logo_path'] = $logoPath;
        }

        if ($iconPath !== null) {
            $data['pwa_icon_path'] = $iconPath;
        }

        $db = Database::connect();
        $db->transBegin();

        if ($this->cafes->update((int) $cafe['id'], $data) === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        if (! $this->cafeLanguages->syncForCafe((int) $cafe['id'], $languageCodes)) {
            $db->transRollback();

            return redirect()->back()->withInput()->with('errors', $this->cafeLanguages->getErrors());
        }

        $db->transCommit();

        $this->cafeService->touchMenuUpdatedAt((int) $cafe['id']);

        return redirect()->to(site_url('admin/settings'))->with('success', 'Настройки кафе обновлены.');
    }

    public function updatePassword()
    {
        $cafe = $this->cafeService->getCurrentCafe();

        if ($cafe === null) {
            return redirect()->to(site_url('login'));
        }

        $rules = [
            'old_password'          => 'required',
            'new_password'          => 'required|min_length[5]|max_length[255]',
            'new_password_confirm'  => 'required|matches[new_password]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldPassword = (string) $this->request->getPost('old_password');
        $newPassword = (string) $this->request->getPost('new_password');

        if (! password_verify($oldPassword, $cafe['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Текущий пароль указан неверно.');
        }

        if (password_verify($newPassword, $cafe['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Новый пароль должен отличаться от текущего.');
        }

        $this->cafes->update((int) $cafe['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        if ($this->cafes->errors() !== []) {
            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        return redirect()->to(site_url('admin/settings'))->with('success', 'Пароль успешно обновлен.');
    }
}

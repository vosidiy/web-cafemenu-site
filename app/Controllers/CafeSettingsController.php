<?php

namespace App\Controllers;

use App\Models\CafeModel;
use App\Services\AdminUiTextCatalogService;
use App\Services\CafeFeeTranslationService;
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
        private readonly CafeFeeTranslationService $feeTranslations = new CafeFeeTranslationService(),
        private readonly AdminUiTextCatalogService $adminTexts = new AdminUiTextCatalogService(),
    ) {
    }

    public function edit(): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();

        return view('admin/settings/edit', [
            'title'              => 'settings_page_title',
            'cafe'               => $this->cafeService->getCurrentCafe(),
            'supportedLanguages' => $this->cafeLanguages->getSupportedLanguages(),
            'cafeLanguages'      => $this->cafeLanguages->getByCafe($cafeId),
            'feeTranslations'    => $this->feeTranslations->getByCafeId($cafeId),
        ]);
    }

    public function update()
    {
        $cafe = $this->cafeService->getCurrentCafe();

        if ($cafe === null) {
            return redirect()->to(site_url('login'));
        }

        try {
            $this->uploads->assertMultipartRequestWithinSizeLimit($this->request);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        $data = [
            'phone'             => trim((string) $this->request->getPost('phone')),
            'person_name'       => trim((string) $this->request->getPost('person_name')),
            'cafe_name'         => trim((string) $this->request->getPost('cafe_name')),
            'slogan'            => trim((string) $this->request->getPost('slogan')),
            'currency_name'     => trim((string) $this->request->getPost('currency_name')) ?: 'USD',
            'theme_style'       => trim((string) $this->request->getPost('theme_style')) ?: 'theme1',
            'address_text'      => trim((string) $this->request->getPost('address_text')),
            'location_url'      => trim((string) $this->request->getPost('location_url')),
            'status'            => $cafe['status'],
        ];

        $languageCodes = $this->cafeLanguages->normalizeLanguageCodes((array) $this->request->getPost('languages'));

        try {
            $logoPath = $this->uploads->storeUploadedImage($this->request->getFile('logo_file'), $cafe['username']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        if ($logoPath !== null) {
            $data['logo_path'] = $logoPath;
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

        return redirect()->to(site_url('admin/settings'))->with('success', $this->adminTexts->translate('cafe_settings_updated'));
    }

    public function updateExtraFee()
    {
        $cafe = $this->cafeService->getCurrentCafe();

        if ($cafe === null) {
            return redirect()->to(site_url('login'));
        }

        $isEnabled = $this->request->getPost('extra_fee_enabled') ? 1 : 0;
        $feeType = strtolower(trim((string) $this->request->getPost('extra_fee_type')));
        $feeValue = trim((string) $this->request->getPost('extra_fee_value'));
        $feeTranslations = $this->collectFeeTranslations();

        $errors = $this->validateExtraFee($isEnabled === 1, $feeType, $feeValue);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $data = [
            'extra_fee_enabled' => $isEnabled,
        ];

        if ($isEnabled === 1) {
            $data['extra_fee_type'] = $feeType;
            $data['extra_fee_value'] = $feeValue;
        }

        $db = Database::connect();
        $db->transBegin();

        if ($this->cafes->update((int) $cafe['id'], $data) === false) {
            $db->transRollback();

            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        if ($isEnabled === 1) {
            $cafeLanguages = $this->getCafeLanguagesForFeeSettings((int) $cafe['id']);

            if (! $this->feeTranslations->syncForCafe((int) $cafe['id'], $cafeLanguages, $feeTranslations, true)) {
                $db->transRollback();

                return redirect()->back()->withInput()->with('errors', $this->feeTranslations->getErrors());
            }
        }

        $db->transCommit();

        $this->cafeService->touchMenuUpdatedAt((int) $cafe['id']);

        return redirect()->to(site_url('admin/settings'))->with('success', $this->adminTexts->translate('extra_fee_updated'));
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
            return redirect()->back()->withInput()->with('error', $this->adminTexts->translate('current_password_incorrect'));
        }

        if (password_verify($newPassword, $cafe['password_hash'])) {
            return redirect()->back()->withInput()->with('error', $this->adminTexts->translate('new_password_must_differ'));
        }

        $this->cafes->update((int) $cafe['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        if ($this->cafes->errors() !== []) {
            return redirect()->back()->withInput()->with('errors', $this->cafes->errors());
        }

        return redirect()->to(site_url('admin/settings'))->with('success', $this->adminTexts->translate('password_updated_success'));
    }

    private function validateExtraFee(bool $isEnabled, string $type, string $value): array
    {
        if (! $isEnabled) {
            return [];
        }

        $errors = [];

        if (! in_array($type, ['fixed', 'percent'], true)) {
            $errors['extra_fee_type'] = $this->adminTexts->translate('select_extra_fee_type');
        }

        if ($value === '' || ! is_numeric($value) || (float) $value <= 0) {
            $errors['extra_fee_value'] = $this->adminTexts->translate('extra_fee_value_gt_zero');
        }

        return $errors;
    }

    private function collectFeeTranslations(): array
    {
        $translations = [];

        foreach ((array) $this->request->getPost('fee_translations') as $languageCode => $row) {
            $translations[(string) $languageCode] = [
                'label' => trim((string) ($row['label'] ?? '')),
            ];
        }

        return $translations;
    }

    private function getCafeLanguagesForFeeSettings(int $cafeId): array
    {
        $cafeLanguages = $this->cafeLanguages->getByCafe($cafeId);

        if ($cafeLanguages === []) {
            return [[
                'language_code' => menu_configured_default_language(),
                'sort_order'    => 1,
            ]];
        }

        return $cafeLanguages;
    }
}

<?php

namespace App\Services;

use App\Models\CafeFeeTranslationModel;

class CafeFeeTranslationService
{
    private array $errors = [];

    public function __construct(
        private readonly CafeFeeTranslationModel $translations = new CafeFeeTranslationModel(),
        private readonly AdminUiTextCatalogService $adminTexts = new AdminUiTextCatalogService(),
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getByCafeId(int $cafeId, array $languageCodes = []): array
    {
        return $this->translations->getByCafeId($cafeId, $languageCodes);
    }

    public function syncForCafe(int $cafeId, array $cafeLanguages, array $payload, bool $requireDefaultLabel): bool
    {
        $languageCodes = array_map(
            static fn (array $language): string => $language['language_code'] ?? $language['code'],
            $cafeLanguages,
        );

        $defaultLanguage = menu_default_language($cafeLanguages, menu_configured_default_language());
        $rows = [];
        $errors = [];

        foreach ($languageCodes as $code) {
            $label = trim((string) ($payload[$code]['label'] ?? ''));

            if ($requireDefaultLabel && $code === $defaultLanguage && $label === '') {
                $errors['fee_translations.' . $code . '.label'] = $this->adminTexts->translate('default_fee_label_required');
                continue;
            }

            if ($label === '') {
                continue;
            }

            $rows[] = [
                'cafe_id'       => $cafeId,
                'language_code' => $code,
                'label'         => $label,
            ];
        }

        $this->errors = $errors;

        if ($errors !== []) {
            return false;
        }

        $this->translations->where('cafe_id', $cafeId)->delete();

        if ($rows !== [] && $this->translations->insertBatch($rows) === false) {
            $this->errors = $this->translations->errors();

            return false;
        }

        return true;
    }
}

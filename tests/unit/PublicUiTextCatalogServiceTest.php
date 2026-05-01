<?php

use App\Services\LanguageCatalogService;
use App\Services\PublicUiTextCatalogService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PublicUiTextCatalogServiceTest extends CIUnitTestCase
{
    public function testCatalogProvidesTranslationsForEverySupportedLanguage(): void
    {
        $languageCatalog = new LanguageCatalogService();
        $uiCatalog = new PublicUiTextCatalogService($languageCatalog);
        $translations = $uiCatalog->getSupportedTranslationsIndexed();

        foreach (array_keys($languageCatalog->getSupportedLanguagesIndexed()) as $languageCode) {
            $this->assertArrayHasKey($languageCode, $translations);
            $this->assertIsString($translations[$languageCode]['menu_language_label'] ?? null);
            $this->assertIsString($translations[$languageCode]['selected_button'] ?? null);
        }
    }

    public function testCatalogFiltersUnknownAndDuplicateLanguageCodes(): void
    {
        $uiCatalog = new PublicUiTextCatalogService();

        $translations = $uiCatalog->getTranslationsForLanguages(['ru', 'xx', 'en', 'ru', '']);

        $this->assertSame(['ru', 'en'], array_keys($translations));
        $this->assertSame('Язык меню', $translations['ru']['menu_language_label']);
        $this->assertSame('Menu language', $translations['en']['menu_language_label']);
    }
}

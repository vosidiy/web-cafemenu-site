<?php

use App\Services\AdminUiTextCatalogService;
use App\Services\LanguageCatalogService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AdminUiTextCatalogServiceTest extends CIUnitTestCase
{
    public function testCatalogProvidesTranslationsForEverySupportedLanguage(): void
    {
        $languageCatalog = new LanguageCatalogService();
        $uiCatalog = new AdminUiTextCatalogService($languageCatalog);
        $translations = $uiCatalog->getSupportedTranslationsIndexed();

        foreach (array_keys($languageCatalog->getSupportedLanguagesIndexed()) as $languageCode) {
            $this->assertArrayHasKey($languageCode, $translations);
            $this->assertIsString($translations[$languageCode]['menu_language_label'] ?? null);
            $this->assertIsString($translations[$languageCode]['save_settings'] ?? null);
        }
    }

    public function testCatalogReturnsNullForUnknownLanguageCode(): void
    {
        $uiCatalog = new AdminUiTextCatalogService();

        $this->assertNull($uiCatalog->getTranslationsForLanguage('xx'));
        $this->assertNull($uiCatalog->getTranslationsForLanguage(''));
    }

    public function testTranslateFallsBackToEnglishForSupportedLanguageWithoutOverride(): void
    {
        $uiCatalog = new AdminUiTextCatalogService();

        $this->assertSame('Support', $uiCatalog->translate('support', 'tr'));
        $this->assertSame('Settings', $uiCatalog->translate('settings_page_title', 'ar'));
    }
}

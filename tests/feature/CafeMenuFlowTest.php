<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Config\Database;

/**
 * @internal
 */
final class CafeMenuFlowTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private static bool $schemaReady = false;

    protected function setUp(): void
    {
        parent::setUp();

        $db = Database::connect('tests');

        if (! self::$schemaReady) {
            $this->createSchema($db);
            self::$schemaReady = true;
        }

        $db->table('menu_item_translations')->emptyTable();
        $db->table('category_translations')->emptyTable();
        $db->table('cafe_languages')->emptyTable();
        $db->table('menu_items')->emptyTable();
        $db->table('categories')->emptyTable();
        $db->table('cafes')->emptyTable();
    }

    public function testProtectedAdminRouteRedirectsGuests(): void
    {
        $result = $this->get('admin');

        $result->assertRedirect();
        $result->assertRedirectTo('login');
    }

    public function testRegistrationCreatesCafeAndDefaultLanguageAndRedirectsToDashboard(): void
    {
        $result = $this->post('register', [
            'username'         => 'bestcafe',
            'phone'            => '+998901234567',
            'person_name'      => 'Ali',
            'cafe_name'        => 'Best Cafe',
            'password'         => 'secret123',
            'password_confirm' => 'secret123',
        ]);

        $result->assertRedirect();
        $result->assertRedirectTo('admin');

        $row = Database::connect('tests')->table('cafes')->where('username', 'bestcafe')->get()->getRowArray();

        $this->assertNotNull($row);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) ($row['code'] ?? ''));
        $this->assertTrue(password_verify('secret123', $row['password_hash']));

        $languageRow = Database::connect('tests')->table('cafe_languages')->where('cafe_id', $row['id'])->get()->getRowArray();

        $this->assertNotNull($languageRow);
        $this->assertSame('ru', $languageRow['language_code']);
        $this->assertSame(1, (int) $languageRow['sort_order']);
    }

    public function testRegistrationNormalizesUppercaseAndSpacesInUsername(): void
    {
        $result = $this->post('register', [
            'username'         => ' Best Cafe ',
            'phone'            => '+998901234567',
            'person_name'      => 'Ali',
            'cafe_name'        => 'Best Cafe',
            'password'         => 'secret123',
            'password_confirm' => 'secret123',
        ]);

        $result->assertRedirectTo('admin');

        $row = Database::connect('tests')->table('cafes')->where('username', 'bestcafe')->get()->getRowArray();

        $this->assertNotNull($row);
    }

    public function testRegistrationCanSucceedWithoutCodeWhenGenerationCollidesRepeatedly(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '123456',
            'username'      => 'existingcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Existing Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $controller = new class extends \App\Controllers\AuthController {
            protected function generateCafeCode(): string
            {
                return '123456';
            }
        };

        $controller->initController(
            Services::request(),
            Services::response(),
            Services::logger(),
        );

        $_POST = [
            'username'         => 'bestcafe',
            'phone'            => '+998998887766',
            'person_name'      => 'Vali',
            'cafe_name'        => 'Other Cafe',
            'password'         => 'secret123',
            'password_confirm' => 'secret123',
        ];

        $response = $controller->store();

        $this->assertTrue($response->isRedirect());

        $row = $db->table('cafes')->where('username', 'bestcafe')->get()->getRowArray();

        $this->assertNotNull($row);
        $this->assertNull($row['code']);
    }

    public function testMenuJsonReturnsMultilingualPayloadAndPublicFilters(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'              => 1,
            'code'            => '123456',
            'username'        => 'bestcafe',
            'phone'           => '+998901234567',
            'person_name'     => 'Ali',
            'cafe_name'       => 'Best Cafe',
            'slogan'          => 'Fresh coffee every day',
            'password_hash'   => password_hash('secret123', PASSWORD_DEFAULT),
            'currency_name'   => 'UZS',
            'theme_style'     => 'theme2',
            'address_text'    => 'Navoi street 12',
            'location_url'    => 'https://maps.google.com/?q=41.55,60.63',
            'menu_updated_at' => '2026-04-02 14:30:00',
            'status'          => 'active',
            'created_at'      => '2026-04-02 14:30:00',
            'updated_at'      => '2026-04-02 14:30:00',
        ]);

        $db->table('cafe_languages')->insertBatch([
            [
                'id'            => 1,
                'cafe_id'       => 1,
                'language_code' => 'ru',
                'sort_order'    => 1,
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 2,
                'cafe_id'       => 1,
                'language_code' => 'en',
                'sort_order'    => 2,
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
        ]);

        $db->table('categories')->insertBatch([
            [
                'id'         => 1,
                'cafe_id'    => 1,
                'sort_order' => 1,
                'is_active'  => 1,
                'created_at' => '2026-04-02 14:30:00',
                'updated_at' => '2026-04-02 14:30:00',
            ],
            [
                'id'         => 2,
                'cafe_id'    => 1,
                'sort_order' => 2,
                'is_active'  => 0,
                'created_at' => '2026-04-02 14:30:00',
                'updated_at' => '2026-04-02 14:30:00',
            ],
        ]);

        $db->table('category_translations')->insertBatch([
            [
                'id'            => 1,
                'category_id'   => 1,
                'language_code' => 'ru',
                'name'          => 'Напитки',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 2,
                'category_id'   => 1,
                'language_code' => 'en',
                'name'          => 'Drinks',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 3,
                'category_id'   => 2,
                'language_code' => 'ru',
                'name'          => 'Скрытое',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
        ]);

        $db->table('menu_items')->insertBatch([
            [
                'id'           => 11,
                'cafe_id'      => 1,
                'category_id'  => 1,
                'price'        => 18000,
                'image_path'   => 'uploads/bestcafe/cappuccino.jpg',
                'is_available' => 1,
                'sort_order'   => 1,
                'created_at'   => '2026-04-02 14:30:00',
                'updated_at'   => '2026-04-02 14:30:00',
            ],
            [
                'id'           => 12,
                'cafe_id'      => 1,
                'category_id'  => 2,
                'price'        => 35000,
                'image_path'   => null,
                'is_available' => 1,
                'sort_order'   => 2,
                'created_at'   => '2026-04-02 14:30:00',
                'updated_at'   => '2026-04-02 14:30:00',
            ],
            [
                'id'           => 13,
                'cafe_id'      => 1,
                'category_id'  => 1,
                'price'        => 16000,
                'image_path'   => null,
                'is_available' => 0,
                'sort_order'   => 3,
                'created_at'   => '2026-04-02 14:30:00',
                'updated_at'   => '2026-04-02 14:30:00',
            ],
        ]);

        $db->table('menu_item_translations')->insertBatch([
            [
                'id'            => 1,
                'menu_item_id'  => 11,
                'language_code' => 'ru',
                'name'          => 'Капучино',
                'description'   => 'Горячий кофе',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 2,
                'menu_item_id'  => 11,
                'language_code' => 'en',
                'name'          => 'Cappuccino',
                'description'   => 'Hot coffee',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 3,
                'menu_item_id'  => 12,
                'language_code' => 'ru',
                'name'          => 'Скрытый бургер',
                'description'   => 'Не должно отображаться',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 4,
                'menu_item_id'  => 13,
                'language_code' => 'ru',
                'name'          => 'Недоступный кофе',
                'description'   => 'Не должно отображаться',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
        ]);

        $result = $this->get('bestcafe/menu.json');

        $result->assertStatus(200);
        $payload = json_decode($result->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('bestcafe', $payload['meta']['username']);
        $this->assertArrayNotHasKey('version', $payload['meta']);
        $this->assertSame('ru', $payload['meta']['default_language']);
        $this->assertCount(2, $payload['meta']['languages']);
        $this->assertSame('Fresh coffee every day', $payload['cafe']['slogan']);
        $this->assertCount(1, $payload['categories']);
        $this->assertArrayNotHasKey('name', $payload['categories'][0]);
        $this->assertSame('Напитки', $payload['categories'][0]['translations']['ru']['name']);
        $this->assertSame('Drinks', $payload['categories'][0]['translations']['en']['name']);
        $this->assertCount(1, $payload['items']);
        $this->assertArrayNotHasKey('name', $payload['items'][0]);
        $this->assertArrayNotHasKey('description', $payload['items'][0]);
        $this->assertSame('Капучино', $payload['items'][0]['translations']['ru']['name']);
        $this->assertSame('Cappuccino', $payload['items'][0]['translations']['en']['name']);
        $this->assertSame('http://example.com/uploads/bestcafe/cappuccino.jpg', $payload['items'][0]['image_url']);
    }

    public function testRegistrationWithDuplicateUsernameFailsWithoutCreatingSecondCafe(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '123456',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->post('register', [
            'username'         => 'bestcafe',
            'phone'            => '+998998887766',
            'person_name'      => 'Vali',
            'cafe_name'        => 'Other Cafe',
            'password'         => 'secret123',
            'password_confirm' => 'secret123',
        ]);

        $result->assertRedirect();

        $cafes = $db->table('cafes')->where('username', 'bestcafe')->get()->getResultArray();

        $this->assertCount(1, $cafes);
    }

    public function testRegistrationTreatsFormattedUsernameVariantsAsDuplicates(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '123456',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->post('register', [
            'username'         => ' BEST CAFE ',
            'phone'            => '+998998887766',
            'person_name'      => 'Vali',
            'cafe_name'        => 'Other Cafe',
            'password'         => 'secret123',
            'password_confirm' => 'secret123',
        ]);

        $result->assertRedirect();

        $cafes = $db->table('cafes')->where('username', 'bestcafe')->get()->getResultArray();

        $this->assertCount(1, $cafes);
    }

    public function testLoginAcceptsUppercaseAndSpacesInUsername(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '123456',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->post('login', [
            'username' => ' Best Cafe ',
            'password' => 'secret123',
        ]);

        $result->assertRedirectTo('admin');
    }

    public function testMenuJsonCanBeFetchedByPairingCode(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'              => 1,
            'code'            => '123456',
            'username'        => 'bestcafe',
            'phone'           => '+998901234567',
            'person_name'     => 'Ali',
            'cafe_name'       => 'Best Cafe',
            'password_hash'   => password_hash('secret123', PASSWORD_DEFAULT),
            'currency_name'   => 'UZS',
            'theme_style'     => 'theme2',
            'menu_updated_at' => '2026-04-02 14:30:00',
            'status'          => 'active',
            'created_at'      => '2026-04-02 14:30:00',
            'updated_at'      => '2026-04-02 14:30:00',
        ]);

        $db->table('cafe_languages')->insert([
            'id'            => 1,
            'cafe_id'       => 1,
            'language_code' => 'ru',
            'sort_order'    => 1,
            'created_at'    => '2026-04-02 14:30:00',
            'updated_at'    => '2026-04-02 14:30:00',
        ]);

        $result = $this->get('code/123456');

        $result->assertStatus(200);
        $payload = json_decode($result->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('bestcafe', $payload['meta']['username']);
        $this->assertSame('Best Cafe', $payload['cafe']['name']);
    }

    public function testMenuJsonByInvalidPairingCodeReturnsNotFound(): void
    {
        $result = $this->get('code/999999');

        $result->assertStatus(404);
    }

    public function testCategoryCreatePersistsTranslationsForEnabledCafeLanguages(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '234567',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $db->table('cafe_languages')->insertBatch([
            ['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 1],
            ['cafe_id' => 1, 'language_code' => 'en', 'sort_order' => 2],
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->post('admin/categories', [
            'sort_order'          => 1,
            'is_active'           => 1,
            'translations'        => [
                'ru' => ['name' => 'Напитки'],
                'en' => ['name' => 'Drinks'],
            ],
        ]);

        $result->assertRedirectTo('admin/categories');

        $category = $db->table('categories')->where('cafe_id', 1)->get()->getRowArray();

        $this->assertNotNull($category);

        $translations = $db->table('category_translations')->where('category_id', $category['id'])->orderBy('language_code', 'ASC')->get()->getResultArray();

        $this->assertCount(2, $translations);
        $this->assertSame('Drinks', $translations[0]['name']);
        $this->assertSame('Напитки', $translations[1]['name']);
    }

    public function testCafeSettingsRejectDuplicateLanguages(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '345678',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $db->table('cafe_languages')->insert(['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 1]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->post('admin/settings', [
            'person_name'   => 'Ali',
            'phone'         => '+998901234567',
            'cafe_name'     => 'Best Cafe',
            'slogan'        => '',
            'currency_name' => 'UZS',
            'theme_style'   => 'theme1',
            'address_text'  => '',
            'location_url'  => '',
            'languages'     => ['ru', 'ru', ''],
        ]);

        $result->assertRedirect();

        $languages = $db->table('cafe_languages')->where('cafe_id', 1)->orderBy('sort_order', 'ASC')->get()->getResultArray();

        $this->assertCount(1, $languages);
        $this->assertSame('ru', $languages[0]['language_code']);
    }

    public function testMenuItemCreateRejectsSecondaryDescriptionWithoutName(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '456789',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $db->table('cafe_languages')->insertBatch([
            ['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 1],
            ['cafe_id' => 1, 'language_code' => 'en', 'sort_order' => 2],
        ]);

        $db->table('categories')->insert([
            'id'         => 1,
            'cafe_id'    => 1,
            'sort_order' => 1,
            'is_active'  => 1,
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->post('admin/menu-items', [
            'category_id'   => 1,
            'price'         => '18000',
            'is_available'  => 1,
            'sort_order'    => 1,
            'translations'  => [
                'ru' => ['name' => 'Капучино', 'description' => 'Горячий кофе'],
                'en' => ['name' => '', 'description' => 'Hot coffee'],
            ],
        ]);

        $result->assertRedirect();
        $this->assertNull($db->table('menu_items')->where('cafe_id', 1)->get()->getRowArray());
    }

    private function createSchema($db): void
    {
        $db->query('
            CREATE TABLE IF NOT EXISTS cafes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(6) DEFAULT NULL UNIQUE,
                username VARCHAR(50) NOT NULL UNIQUE,
                phone VARCHAR(30) NOT NULL,
                person_name VARCHAR(150) NOT NULL,
                cafe_name VARCHAR(150) DEFAULT NULL,
                slogan VARCHAR(255) DEFAULT NULL,
                password_hash VARCHAR(255) NOT NULL,
                logo_path VARCHAR(255) DEFAULT NULL,
                pwa_icon_path VARCHAR(255) DEFAULT NULL,
                currency_name VARCHAR(20) NOT NULL DEFAULT "UZS",
                theme_style VARCHAR(20) NOT NULL DEFAULT "theme1",
                address_text VARCHAR(255) DEFAULT NULL,
                location_url VARCHAR(500) DEFAULT NULL,
                menu_updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                status VARCHAR(20) NOT NULL DEFAULT "active",
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->query('
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cafe_id INTEGER NOT NULL,
                sort_order INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->query('
            CREATE TABLE IF NOT EXISTS menu_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cafe_id INTEGER NOT NULL,
                category_id INTEGER DEFAULT NULL,
                price DECIMAL(10,2) NOT NULL,
                image_path VARCHAR(255) DEFAULT NULL,
                is_available INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->query('
            CREATE TABLE IF NOT EXISTS cafe_languages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cafe_id INTEGER NOT NULL,
                language_code VARCHAR(10) NOT NULL,
                sort_order INTEGER NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->query('
            CREATE TABLE IF NOT EXISTS category_translations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NOT NULL,
                language_code VARCHAR(10) NOT NULL,
                name VARCHAR(100) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->query('
            CREATE TABLE IF NOT EXISTS menu_item_translations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                menu_item_id INTEGER NOT NULL,
                language_code VARCHAR(10) NOT NULL,
                name VARCHAR(150) NOT NULL,
                description TEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }
}

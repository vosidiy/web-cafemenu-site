<?php

use App\Services\AdminUiTextCatalogService;
use App\Controllers\CategoryController;
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
    private string $originalActivationUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalActivationUrl = (string) config('App')->activationUrl;

        $db = Database::connect('tests');

        if (! self::$schemaReady) {
            $this->createSchema($db);
            self::$schemaReady = true;
        }

        $db->table('cafe_fee_translations')->emptyTable();
        $db->table('menu_item_translations')->emptyTable();
        $db->table('category_translations')->emptyTable();
        $db->table('cafe_languages')->emptyTable();
        $db->table('menu_items')->emptyTable();
        $db->table('categories')->emptyTable();
        $db->table('cafes')->emptyTable();
    }

    protected function tearDown(): void
    {
        config('App')->activationUrl = $this->originalActivationUrl;

        parent::tearDown();
    }

    public function testProtectedAdminRouteRedirectsGuests(): void
    {
        $result = $this->get('admin');

        $result->assertRedirect();
        $result->assertRedirectTo('login');
    }

    public function testRegistrationCreatesCafeAndEnglishDefaultLanguageAndRedirectsToDashboard(): void
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
        $this->assertSame('demo', $row['status']);

        $languageRow = Database::connect('tests')->table('cafe_languages')->where('cafe_id', $row['id'])->get()->getRowArray();

        $this->assertNotNull($languageRow);
        $this->assertSame('en', $languageRow['language_code']);
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
        $this->assertSame('demo', $row['status']);
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
        $this->assertSame('demo', $row['status']);
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
            'extra_fee_enabled' => 1,
            'extra_fee_type'  => 'percent',
            'extra_fee_value' => 5,
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

        $db->table('cafe_fee_translations')->insertBatch([
            [
                'id'            => 1,
                'cafe_id'       => 1,
                'language_code' => 'ru',
                'label'         => 'Сервисный сбор',
                'created_at'    => '2026-04-02 14:30:00',
                'updated_at'    => '2026-04-02 14:30:00',
            ],
            [
                'id'            => 2,
                'cafe_id'       => 1,
                'language_code' => 'en',
                'label'         => 'Service fee',
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
                'icon_path'  => 'uploads/bestcafe/drinks-icon.png',
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
        $this->assertSame('ru-RU', $payload['meta']['languages'][0]['locale']);
        $this->assertSame('en-GB', $payload['meta']['languages'][1]['locale']);
        $this->assertSame('Fresh coffee every day', $payload['cafe']['slogan']);
        $this->assertTrue($payload['cafe']['extra_fee']['enabled']);
        $this->assertSame('percent', $payload['cafe']['extra_fee']['type']);
        $this->assertSame(5.0, $payload['cafe']['extra_fee']['value']);
        $this->assertSame('Сервисный сбор', $payload['cafe']['extra_fee']['translations']['ru']['label']);
        $this->assertSame('Service fee', $payload['cafe']['extra_fee']['translations']['en']['label']);
        $this->assertCount(1, $payload['categories']);
        $this->assertArrayNotHasKey('name', $payload['categories'][0]);
        $this->assertSame('http://example.com/uploads/bestcafe/drinks-icon.png', $payload['categories'][0]['icon_url']);
        $this->assertSame('Напитки', $payload['categories'][0]['translations']['ru']['name']);
        $this->assertSame('Drinks', $payload['categories'][0]['translations']['en']['name']);
        $this->assertCount(1, $payload['items']);
        $this->assertArrayNotHasKey('name', $payload['items'][0]);
        $this->assertArrayNotHasKey('description', $payload['items'][0]);
        $this->assertSame('Капучино', $payload['items'][0]['translations']['ru']['name']);
        $this->assertSame('Cappuccino', $payload['items'][0]['translations']['en']['name']);
        $this->assertSame('http://example.com/uploads/bestcafe/cappuccino.jpg', $payload['items'][0]['image_url']);
        $this->assertSame(['ru', 'en'], array_keys($payload['ui_translations']));
        $this->assertSame('Выбрано', $payload['ui_translations']['ru']['selected_button']);
        $this->assertSame('Selected', $payload['ui_translations']['en']['selected_button']);
        $this->assertSame('Выбрано позиций: {count}', $payload['ui_translations']['ru']['cart_bar_selected_count']);
        $this->assertArrayNotHasKey('tr', $payload['ui_translations']);
        $this->assertArrayNotHasKey('pwa_icon_url', $payload['cafe']);
    }

    public function testMenuJsonFallsBackToConfiguredEnglishDefaultWhenCafeHasNoLanguageRows(): void
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
            'menu_updated_at' => '2026-04-02 14:30:00',
            'status'          => 'active',
            'created_at'      => '2026-04-02 14:30:00',
            'updated_at'      => '2026-04-02 14:30:00',
        ]);

        $result = $this->get('bestcafe/menu.json');

        $result->assertStatus(200);
        $payload = json_decode($result->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('en', $payload['meta']['default_language']);
        $this->assertSame('en', $payload['meta']['languages'][0]['code']);
        $this->assertSame('en-GB', $payload['meta']['languages'][0]['locale']);
        $this->assertFalse($payload['cafe']['extra_fee']['enabled']);
        $this->assertNull($payload['cafe']['extra_fee']['type']);
        $this->assertNull($payload['cafe']['extra_fee']['value']);
        $this->assertSame([], $payload['cafe']['extra_fee']['translations']);
        $this->assertSame(['en'], array_keys($payload['ui_translations']));
        $this->assertSame('Menu language', $payload['ui_translations']['en']['menu_language_label']);
    }

    public function testDemoCafeMenuShellShowsActivationNotice(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'democafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Demo Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'demo',
        ]);

        $result = $this->get('democafe');

        $result->assertStatus(200);
        $result->assertSee('Demo cafe.');
        $result->assertSee('Activate now');
        $result->assertSee('https://pay.example.com/activate');
        $result->assertSee('/menu-favicon/apple-touch-icon.png');
        $result->assertSee('/menu-favicon/favicon-32x32.png');
        $result->assertSee('/menu-favicon/favicon-16x16.png');
        $result->assertSee('/menu-favicon/site.webmanifest');
        $result->assertDontSee('/democafe/manifest.webmanifest');
        $result->assertDontSee('Install app');
        $result->assertDontSee('/sw.js');
    }

    public function testInactiveCafeSlugShowsActivationPage(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'sleepycafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Sleepy Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'inactive',
        ]);

        $result = $this->get('sleepycafe');

        $result->assertStatus(200);
        $result->assertSee('Cafe is deactivated');
        $result->assertSee('Activate cafe');
        $result->assertSee('https://pay.example.com/activate');
    }

    public function testInactiveCafeMenuJsonReturnsInactiveEnvelope(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

        $db->table('cafes')->insert([
            'id'              => 1,
            'code'            => '123456',
            'username'        => 'sleepycafe',
            'phone'           => '+998901234567',
            'person_name'     => 'Ali',
            'cafe_name'       => 'Sleepy Cafe',
            'password_hash'   => password_hash('secret123', PASSWORD_DEFAULT),
            'menu_updated_at' => '2026-04-02 14:30:00',
            'status'          => 'inactive',
            'created_at'      => '2026-04-02 14:30:00',
            'updated_at'      => '2026-04-02 14:30:00',
        ]);

        $result = $this->get('sleepycafe/menu.json');

        $result->assertStatus(200);
        $payload = json_decode($result->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('inactive', $payload['public_status']);
        $this->assertSame('inactive', $payload['cafe']['status']);
        $this->assertSame('https://pay.example.com/activate', $payload['cafe']['activation_url']);
        $this->assertSame([], $payload['categories']);
        $this->assertSame([], $payload['items']);
        $this->assertFalse($payload['cafe']['extra_fee']['enabled']);
    }

    public function testDemoCafeSeesAdminActivationBannerOnDashboard(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'democafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Demo Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'demo',
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'democafe',
        ])->get('admin');

        $result->assertStatus(200);
        $result->assertSee('Cafe is in demo mode. Activate it to go live.');
        $result->assertSee('https://pay.example.com/activate');
    }

    public function testInactiveCafeSeesAdminActivationBannerOnCategoriesPage(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'sleepycafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Sleepy Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'inactive',
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'sleepycafe',
        ])->get('admin/categories');

        $result->assertStatus(200);
        $result->assertSee('Cafe is deactivated. Activate it to restore public access.');
        $result->assertSee('https://pay.example.com/activate');
    }

    public function testActiveCafeDoesNotSeeAdminActivationBanner(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'activecafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Active Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'activecafe',
        ])->get('admin');

        $result->assertStatus(200);
        $result->assertDontSee('Activate it to go live.');
        $result->assertDontSee('Activate it to restore public access.');
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

    public function testDemoCafeCanLogin(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'democafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Demo Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'demo',
        ]);

        $result = $this->post('login', [
            'username' => 'democafe',
            'password' => 'secret123',
        ]);

        $result->assertRedirectTo('admin');
    }

    public function testInactiveCafeCannotLogin(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'sleepycafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Sleepy Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'inactive',
        ]);

        $result = $this->post('login', [
            'username' => 'sleepycafe',
            'password' => 'secret123',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testMenuJsonCanBeFetchedByPairingCode(): void
    {
        $db = Database::connect('tests');
        config('App')->activationUrl = 'https://pay.example.com/activate';

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
        $this->assertSame('https://pay.example.com/activate', $payload['cafe']['activation_url']);
    }

    public function testInactiveCafeMenuJsonCanBeFetchedByPairingCodeAsInactiveEnvelope(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'              => 1,
            'code'            => '123456',
            'username'        => 'sleepycafe',
            'phone'           => '+998901234567',
            'person_name'     => 'Ali',
            'cafe_name'       => 'Sleepy Cafe',
            'password_hash'   => password_hash('secret123', PASSWORD_DEFAULT),
            'menu_updated_at' => '2026-04-02 14:30:00',
            'status'          => 'inactive',
            'created_at'      => '2026-04-02 14:30:00',
            'updated_at'      => '2026-04-02 14:30:00',
        ]);

        $result = $this->get('code/123456');

        $result->assertStatus(200);
        $payload = json_decode($result->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('inactive', $payload['public_status']);
        $this->assertSame([], $payload['items']);
    }

    public function testSettingsUpdatePreservesStoredCafeStatus(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'username'      => 'democafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Demo Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'currency_name' => 'UZS',
            'theme_style'   => 'theme1',
            'status'        => 'demo',
        ]);

        $db->table('cafe_languages')->insert([
            'cafe_id'       => 1,
            'language_code' => 'en',
            'sort_order'    => 1,
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'democafe',
        ])->post('admin/settings', [
            'phone'         => '+998907770011',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Demo Cafe Updated',
            'slogan'        => 'New slogan',
            'currency_name' => 'UZS',
            'theme_style'   => 'theme1',
            'address_text'  => '',
            'location_url'  => '',
            'languages'     => ['en'],
        ]);

        $result->assertRedirectTo('admin/settings');

        $row = $db->table('cafes')->where('id', 1)->get()->getRowArray();

        $this->assertSame('demo', $row['status']);
        $this->assertSame('Demo Cafe Updated', $row['cafe_name']);
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
        $this->assertNull($category['icon_path']);

        $translations = $db->table('category_translations')->where('category_id', $category['id'])->orderBy('language_code', 'ASC')->get()->getResultArray();

        $this->assertCount(2, $translations);
        $this->assertSame('Drinks', $translations[0]['name']);
        $this->assertSame('Напитки', $translations[1]['name']);
    }

    public function testCategoryCreateCanPersistMockedIconPath(): void
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

        $db->table('cafe_languages')->insert(['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 1]);

        $_SESSION['cafe_id'] = 1;
        $_SESSION['username'] = 'bestcafe';

        $response = $this->postCategoryThroughController([
            'sort_order'   => 1,
            'is_active'    => 1,
            'translations' => [
                'ru' => ['name' => 'Напитки'],
            ],
        ], null, 'uploads/bestcafe/drinks.png');

        $this->assertTrue($response->isRedirect());

        $category = $db->table('categories')->where('cafe_id', 1)->get()->getRowArray();

        $this->assertNotNull($category);
        $this->assertSame('uploads/bestcafe/drinks.png', $category['icon_path']);
    }

    public function testCategoryUpdatePreservesExistingIconWhenNoNewUploadProvided(): void
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

        $db->table('cafe_languages')->insert(['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 1]);
        $db->table('categories')->insert([
            'id'         => 1,
            'cafe_id'    => 1,
            'sort_order' => 1,
            'is_active'  => 1,
            'icon_path'  => 'uploads/bestcafe/original.png',
        ]);
        $db->table('category_translations')->insert([
            'category_id'   => 1,
            'language_code' => 'ru',
            'name'          => 'Напитки',
        ]);

        $_SESSION['cafe_id'] = 1;
        $_SESSION['username'] = 'bestcafe';

        $response = $this->postCategoryThroughController([
            'sort_order'   => 2,
            'is_active'    => 1,
            'translations' => [
                'ru' => ['name' => 'Напитки'],
            ],
        ], 1, null);

        $this->assertTrue($response->isRedirect());

        $category = $db->table('categories')->where('id', 1)->get()->getRowArray();

        $this->assertSame('uploads/bestcafe/original.png', $category['icon_path']);
        $this->assertSame(2, (int) $category['sort_order']);
    }

    public function testCategoryUpdateCanRemoveExistingIcon(): void
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

        $db->table('cafe_languages')->insert(['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 1]);
        $db->table('categories')->insert([
            'id'         => 1,
            'cafe_id'    => 1,
            'sort_order' => 1,
            'is_active'  => 1,
            'icon_path'  => 'uploads/bestcafe/original.png',
        ]);
        $db->table('category_translations')->insert([
            'category_id'   => 1,
            'language_code' => 'ru',
            'name'          => 'Напитки',
        ]);

        $_SESSION['cafe_id'] = 1;
        $_SESSION['username'] = 'bestcafe';

        $response = $this->postCategoryThroughController([
            'sort_order'   => 2,
            'is_active'    => 1,
            'remove_icon'  => 1,
            'translations' => [
                'ru' => ['name' => 'Напитки'],
            ],
        ], 1, null);

        $this->assertTrue($response->isRedirect());

        $category = $db->table('categories')->where('id', 1)->get()->getRowArray();

        $this->assertNull($category['icon_path']);
    }

    public function testAdminCategoriesIndexShowsEnabledTranslationsOnly(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '345679',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $db->table('cafe_languages')->insertBatch([
            ['cafe_id' => 1, 'language_code' => 'en', 'sort_order' => 1],
            ['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 2],
        ]);

        $db->table('categories')->insert([
            'id'         => 1,
            'cafe_id'    => 1,
            'sort_order' => 1,
            'is_active'  => 1,
        ]);

        $db->table('category_translations')->insertBatch([
            ['category_id' => 1, 'language_code' => 'en', 'name' => 'Drinks'],
            ['category_id' => 1, 'language_code' => 'ru', 'name' => 'напитки'],
            ['category_id' => 1, 'language_code' => 'de', 'name' => 'Getranke'],
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->get('admin/categories');

        $result->assertStatus(200);

        $body = (string) $result->getBody();

        $this->assertStringContainsString('🇺🇸 Drinks', $body);
        $this->assertStringContainsString('🇷🇺 напитки', $body);
        $this->assertStringNotContainsString('Getranke', $body);
        $this->assertLessThan(
            strpos($body, '🇷🇺 напитки'),
            strpos($body, '🇺🇸 Drinks'),
        );
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

        $db->table('cafe_languages')->insert(['cafe_id' => 1, 'language_code' => 'en', 'sort_order' => 1]);

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
            'languages'     => ['en', 'en', ''],
        ]);

        $result->assertRedirect();

        $languages = $db->table('cafe_languages')->where('cafe_id', 1)->orderBy('sort_order', 'ASC')->get()->getResultArray();

        $this->assertCount(1, $languages);
        $this->assertSame('en', $languages[0]['language_code']);
    }

    public function testCafeSettingsCanPersistFixedExtraFeeAndExposeItInMenuJson(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '445566',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $db->table('cafe_languages')->insert(['cafe_id' => 1, 'language_code' => 'en', 'sort_order' => 1]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->post('admin/settings', [
            'person_name'       => 'Ali',
            'phone'             => '+998901234567',
            'cafe_name'         => 'Best Cafe',
            'slogan'            => '',
            'currency_name'     => 'USD',
            'theme_style'       => 'theme1',
            'address_text'      => '',
            'location_url'      => '',
            'languages'         => ['en', '', ''],
            'extra_fee_enabled' => '1',
            'extra_fee_type'    => 'fixed',
            'extra_fee_value'   => '10.00',
            'fee_translations'  => [
                'en' => ['label' => 'Delivery fee'],
            ],
        ]);

        $result->assertRedirectTo('admin/settings');

        $cafe = $db->table('cafes')->where('id', 1)->get()->getRowArray();
        $translation = $db->table('cafe_fee_translations')->where('cafe_id', 1)->where('language_code', 'en')->get()->getRowArray();

        $this->assertSame(1, (int) $cafe['extra_fee_enabled']);
        $this->assertSame('fixed', $cafe['extra_fee_type']);
        $this->assertSame('10.00', (string) $cafe['extra_fee_value']);
        $this->assertNotNull($translation);
        $this->assertSame('Delivery fee', $translation['label']);

        $payload = json_decode($this->get('bestcafe/menu.json')->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($payload['cafe']['extra_fee']['enabled']);
        $this->assertSame('fixed', $payload['cafe']['extra_fee']['type']);
        $this->assertSame(10.0, $payload['cafe']['extra_fee']['value']);
        $this->assertSame('Delivery fee', $payload['cafe']['extra_fee']['translations']['en']['label']);
    }

    public function testCafeSettingsCanPersistPercentExtraFeeAndExposeItInMenuJson(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '556677',
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
        ])->post('admin/settings', [
            'person_name'       => 'Ali',
            'phone'             => '+998901234567',
            'cafe_name'         => 'Best Cafe',
            'slogan'            => '',
            'currency_name'     => 'USD',
            'theme_style'       => 'theme1',
            'address_text'      => '',
            'location_url'      => '',
            'languages'         => ['ru', 'en', ''],
            'extra_fee_enabled' => '1',
            'extra_fee_type'    => 'percent',
            'extra_fee_value'   => '5.00',
            'fee_translations'  => [
                'ru' => ['label' => 'Сервисный сбор'],
                'en' => ['label' => 'Service fee'],
            ],
        ]);

        $result->assertRedirectTo('admin/settings');

        $cafe = $db->table('cafes')->where('id', 1)->get()->getRowArray();
        $translations = $db->table('cafe_fee_translations')->where('cafe_id', 1)->orderBy('language_code', 'ASC')->get()->getResultArray();

        $this->assertSame(1, (int) $cafe['extra_fee_enabled']);
        $this->assertSame('percent', $cafe['extra_fee_type']);
        $this->assertSame('5.00', (string) $cafe['extra_fee_value']);
        $this->assertCount(2, $translations);

        $payload = json_decode($this->get('bestcafe/menu.json')->getJSON(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($payload['cafe']['extra_fee']['enabled']);
        $this->assertSame('percent', $payload['cafe']['extra_fee']['type']);
        $this->assertSame(5.0, $payload['cafe']['extra_fee']['value']);
        $this->assertSame('Сервисный сбор', $payload['cafe']['extra_fee']['translations']['ru']['label']);
        $this->assertSame('Service fee', $payload['cafe']['extra_fee']['translations']['en']['label']);
    }

    public function testCategoryCreateShowsUploadTooLargeErrorWhenMultipartRequestExceedsPostMaxSize(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '556678',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->withHeaders($this->multipartTooLargeHeaders())
            ->post('admin/categories', []);

        $result->assertRedirect();
        $result->assertSessionHas('error', $this->uploadTooLargeMessage('post_max_size'));
    }

    public function testCafeSettingsShowsUploadTooLargeErrorWhenMultipartRequestExceedsPostMaxSize(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '556679',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->withHeaders($this->multipartTooLargeHeaders())
            ->post('admin/settings', []);

        $result->assertRedirect();
        $result->assertSessionHas('error', $this->uploadTooLargeMessage('post_max_size'));
    }

    public function testMenuItemCreateShowsUploadTooLargeErrorInsteadOfCategoryOwnershipMessageWhenMultipartRequestExceedsPostMaxSize(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '556680',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->withHeaders($this->multipartTooLargeHeaders())
            ->post('admin/menu-items', []);

        $result->assertRedirect();
        $result->assertSessionHas('error', $this->uploadTooLargeMessage('post_max_size'));
        $this->assertNotSame($this->selectedCategoryNotOwnedMessage(), $_SESSION['error'] ?? null);
        $this->assertNull($db->table('menu_items')->where('cafe_id', 1)->get()->getRowArray());
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

    public function testAdminMenuItemsIndexShowsEnabledTranslationsAndSearchesAcrossThem(): void
    {
        $db = Database::connect('tests');

        $db->table('cafes')->insert([
            'id'            => 1,
            'code'          => '567890',
            'username'      => 'bestcafe',
            'phone'         => '+998901234567',
            'person_name'   => 'Ali',
            'cafe_name'     => 'Best Cafe',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'status'        => 'active',
        ]);

        $db->table('cafe_languages')->insertBatch([
            ['cafe_id' => 1, 'language_code' => 'en', 'sort_order' => 1],
            ['cafe_id' => 1, 'language_code' => 'ru', 'sort_order' => 2],
        ]);

        $db->table('categories')->insert([
            'id'         => 1,
            'cafe_id'    => 1,
            'sort_order' => 1,
            'is_active'  => 1,
        ]);

        $db->table('category_translations')->insertBatch([
            ['category_id' => 1, 'language_code' => 'en', 'name' => 'Drinks'],
            ['category_id' => 1, 'language_code' => 'ru', 'name' => 'Напитки'],
        ]);

        $db->table('menu_items')->insert([
            'id'           => 1,
            'cafe_id'      => 1,
            'category_id'  => 1,
            'price'        => 18000,
            'image_path'   => null,
            'is_available' => 1,
            'sort_order'   => 1,
        ]);

        $db->table('menu_item_translations')->insertBatch([
            ['menu_item_id' => 1, 'language_code' => 'en', 'name' => 'Coca cola drink', 'description' => 'Some text about coca cola'],
            ['menu_item_id' => 1, 'language_code' => 'ru', 'name' => 'напиток Кока-кола', 'description' => 'Текст о кока-коле'],
            ['menu_item_id' => 1, 'language_code' => 'de', 'name' => 'Cola Getrank', 'description' => 'Sollte versteckt bleiben'],
        ]);

        $result = $this->withSession([
            'cafe_id'  => 1,
            'username' => 'bestcafe',
        ])->get('admin/menu-items');

        $result->assertStatus(200);

        $body = (string) $result->getBody();

        $this->assertStringContainsString('🇺🇸 Coca cola drink', $body);
        $this->assertStringContainsString('Some text about coca cola', $body);
        $this->assertStringContainsString('🇷🇺 напиток Кока-кола', $body);
        $this->assertStringContainsString('Текст о кока-коле', $body);
        $this->assertStringNotContainsString('Cola Getrank', $body);
        $this->assertStringContainsString('data-name="coca cola drink some text about coca cola напиток кока-кола текст о кока-коле"', mb_strtolower($body, 'UTF-8'));
        $this->assertLessThan(
            strpos($body, '🇷🇺 напиток Кока-кола'),
            strpos($body, '🇺🇸 Coca cola drink'),
        );
    }

    private function postCategoryThroughController(array $post, ?int $categoryId = null, ?string $storedIconPath = null)
    {
        Services::resetSingle('incomingrequest');
        Services::resetSingle('response');

        $request = service('incomingrequest', config(\Config\App::class), false);
        $request->setMethod('post');
        $request->setGlobal('post', $post);
        $request->setGlobal('request', $post);

        $controller = new class($storedIconPath) extends CategoryController {
            public function __construct(
                private readonly ?string $mockStoredIconPath,
            ) {
                parent::__construct();
            }

            protected function storeCategoryIcon(string $username): ?string
            {
                return $this->mockStoredIconPath;
            }
        };

        $controller->initController(
            $request,
            service('response', null, false),
            Services::logger(),
        );

        return $categoryId === null ? $controller->create() : $controller->update($categoryId);
    }

    /**
     * @return array<string, string>
     */
    private function multipartTooLargeHeaders(): array
    {
        return [
            'Content-Type'   => 'multipart/form-data; boundary=----cafemenu-boundary',
            'Content-Length' => (string) ($this->parseIniSizeToBytes((string) ini_get('post_max_size')) + 1),
        ];
    }

    private function uploadTooLargeMessage(string $iniKey): string
    {
        return (new AdminUiTextCatalogService())->translate('upload_file_too_large', 'en', [
            'limit' => $this->formatIniSize((string) ini_get($iniKey)),
        ]);
    }

    private function selectedCategoryNotOwnedMessage(): string
    {
        return (new AdminUiTextCatalogService())->translate('selected_category_not_owned', 'en');
    }

    private function parseIniSizeToBytes(string $value): int
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return 0;
        }

        $unit = strtoupper(substr($normalized, -1));
        $size = (float) $normalized;

        return match ($unit) {
            'G'     => (int) round($size * 1024 ** 3),
            'M'     => (int) round($size * 1024 ** 2),
            'K'     => (int) round($size * 1024),
            default => (int) round($size),
        };
    }

    private function formatIniSize(string $value): string
    {
        $normalized = strtoupper(trim($value));

        return $normalized !== '' ? $normalized : '0';
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
                currency_name VARCHAR(20) NOT NULL DEFAULT "UZS",
                theme_style VARCHAR(20) NOT NULL DEFAULT "theme1",
                address_text VARCHAR(255) DEFAULT NULL,
                location_url VARCHAR(500) DEFAULT NULL,
                extra_fee_enabled INTEGER NOT NULL DEFAULT 0,
                extra_fee_type VARCHAR(20) DEFAULT NULL,
                extra_fee_value DECIMAL(10,2) DEFAULT NULL,
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
                icon_path VARCHAR(255) DEFAULT NULL,
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

        $db->query('
            CREATE TABLE IF NOT EXISTS cafe_fee_translations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cafe_id INTEGER NOT NULL,
                language_code VARCHAR(10) NOT NULL,
                label VARCHAR(100) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }
}

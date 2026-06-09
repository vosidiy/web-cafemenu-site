# CafeMenu Architecture

## Overview

CafeMenu is a CodeIgniter 4 application for managing and publishing cafe or restaurant menus. The backend is PHP-based, the database is managed through SQL files instead of CodeIgniter migrations, and the public menu UI is a Vue-powered shell that fetches menu data from a JSON endpoint.

The app has two main surfaces:

- Marketing pages for the product landing experience.
- A tenant-based admin and public menu system for individual cafes.

The multi-tenant model is simple: one cafe account maps to one row in `cafes`, and the cafe `username` serves both as the login identifier and the public URL slug. A cafe may also have an optional 6-digit pairing `code` for app-friendly JSON access.

Menu content is multilingual per cafe. Each cafe can enable 1 to 3 languages in `cafe_languages`, and public clients receive both translated menu content and a translated UI-text bundle for the enabled languages.

Key implementation facts:

- PHP requirement is `^8.2` in `composer.json`.
- Routing uses defined routes only; auto-routing is disabled.
- Schema changes are maintained in SQL files under `database/`.
- Uploaded menu assets are stored under `public/uploads/{username}/`.

## Runtime Surfaces

### Marketing surface

- `/` -> `Home::index`
- `/ru` -> `Home::index_ru`
- `/thankyou` -> static thank-you view

The marketing controllers render landing-page views and show recently created active cafes via `CafeModel::findRecentActive()`.

### Admin surface

Auth routes are top-level:

- `GET /register`
- `POST /register`
- `GET /login`
- `POST /login`
- `GET /logout`

Authenticated routes guarded by `adminauth`:

- `GET /admin`
- `GET /admin/settings`
- `POST /admin/settings`
- `POST /admin/settings/extra-fee`
- `POST /admin/settings/password`
- Category CRUD under `/admin/categories`
- Menu item CRUD under `/admin/menu-items`

### Superadmin surface

Superadmin routes are separate from the cafe-owner admin:

- `GET /superadmin/login`
- `POST /superadmin/login`
- `GET /superadmin/logout`
- `GET /superadmin`
- `GET /superadmin/settings`
- `POST /superadmin/settings`
- `GET /superadmin/account`
- `POST /superadmin/account`
- `GET /superadmin/cafes/{id}/edit`
- `POST /superadmin/cafes/{id}`
- `POST /superadmin/cafes/{id}/password`

Protected superadmin routes are guarded by `superadminauth`.

### Public tenant surface

Each cafe is published under `/{username}`.

- `GET /{username}` -> public HTML shell
- `GET /{username}/menu.json` -> public JSON
- `GET /{username}/menu` -> JSON alias to the same controller
- `GET /code/{6-digit-code}` -> public JSON alias by cafe pairing code

## Routing Model

Routes are defined in `app/Config/Routes.php`. Auto-routing is disabled in `app/Config/Routing.php`, so only explicitly declared routes are reachable.

Route ordering matters:

1. Marketing routes are declared first.
2. Top-level auth routes, `/superadmin` routes, and `/admin` routes are declared next.
3. Code-based and tenant JSON routes are declared next.
4. The final catch-all route is `GET /(:segment)` -> `PublicController::index/$1`.

This means:

- Any future top-level route must be declared before the final `(:segment)` route.
- Any future tenant-specific route must be declared before the catch-all `/{username}` route.
- `/{username}/menu` currently returns JSON, not an HTML page.

## Request Flows

### Registration and login

Registration and login are handled by `AuthController`.

Registration flow:

1. Read POST values for username, phone, person name, cafe name, optional currency, and theme.
2. Normalize username by trimming, removing all whitespace, and lowercasing.
3. Validate request data using the normalized username, including a maximum 6-character currency when provided.
4. Hash the password with `password_hash()`.
5. Use `USD` when the registration currency is blank and set the new cafe status to `demo`.
6. Attempt to insert a row into `cafes` with a randomly generated 6-digit pairing code.
7. If code collisions keep happening, insert the cafe with `code = NULL` and continue registration.
8. Insert the default cafe language row from the centralized app language configuration, currently `en`.
9. Regenerate the session.
10. Store `cafe_id` and normalized `username` in session.
11. Redirect to `/admin`.

Login flow:

1. Normalize the submitted username by trimming, removing all whitespace, and lowercasing.
2. Look up cafe by normalized username.
3. Ensure the cafe exists, is `active`, and the password verifies.
4. Regenerate the session.
5. Store `cafe_id` and `username` in session.
6. Redirect to `/admin`.

Protected admin routes use `AdminAuthFilter`, which redirects unauthenticated users to `/login`.

### Superadmin flow

Superadmin login is handled by `SuperAdminController` against the singleton `admin` row. Successful login regenerates the session and stores `superadmin_id` and `superadmin_username`. Superadmin pages can list all cafes, edit selected basic cafe fields, change a cafe password, update global links/settings, and change the superadmin username/password after confirming the current password.

### Admin CRUD flow

Category and menu item CRUD follow the same tenant pattern:

1. Resolve current cafe from session via `CafeService`.
2. Load or mutate only rows that belong to that cafe.
3. Save non-translatable fields in `categories` / `menu_items`.
4. Save translations in `category_translations` / `menu_item_translations`.
5. On successful create, update, delete, or settings change, call `CafeService::touchMenuUpdatedAt()`.

Cafe settings updates persist tenant-scoped language rows. Extra fee settings are saved through a separate settings form and endpoint, preserving saved fee details when the fee is disabled.

Ownership enforcement is implemented in code:

- Categories are resolved with `CategoryModel::findByCafe()`.
- Menu items are resolved with `MenuItemModel::findByCafe()`.
- Menu item category assignment is checked to ensure the chosen category belongs to the current cafe.

### Public menu flow

The public menu is split into shell and data:

1. `PublicController::index($username)` resolves a cafe by username.
2. For `active` and `demo`, it renders `app/Views/public/menu_shell.php`.
3. For `inactive`, it renders a dedicated activation page instead of a 404.
4. The shell links static favicon assets and a static manifest from `/menu-favicon/`.
5. The shell view exposes `window.MenuAppConfig` containing the JSON URL, placeholder image, and default cafe name.
6. `public/app.js` bootstraps a Vue app.
7. The Vue app fetches `/{username}/menu.json`.
8. The client chooses a menu language from localStorage, browser preference, or the cafe default language.
9. The client renders categories, translated menu items, translated shell UI labels, and a local in-browser selection cart.
10. When configured, the client applies the cafe's fixed or percentage cart fee to the displayed grand total.
11. When the cafe status is `demo`, the shell shows an activation notice in the top bar using the centralized activation URL.
12. Admin pages render a shared activation banner for `demo` and `inactive` cafes through the admin layout.

The cart is client-side only. There is no ordering, checkout, or server-side cart persistence.

## Layer Responsibilities

### Controllers

Controllers are thin request handlers.

- `Home` renders landing pages and recent active cafes.
- `AuthController` handles registration, login, logout, username normalization, and best-effort pairing-code generation.
- `AdminLanguageController` persists the selected admin UI language and redirects back to the current page.
- `AdminController` loads dashboard data and public URLs for the current cafe.
- `CafeSettingsController` updates cafe profile fields, logo, extra fee settings, and password while preserving the stored cafe status.
- `CategoryController` manages category CRUD for the current cafe.
- `MenuItemController` manages menu item CRUD and image uploads for the current cafe.
- `MenuJsonController` returns the normalized public JSON payload.
- `PublicController` renders the tenant menu shell.
- `SuperAdminController` handles platform-admin login, all-cafe listing, cafe basic edits, cafe password updates, global settings, and superadmin account updates.

### Services

- `CafeService`
  - Resolves the current cafe from session.
  - Resolves a cafe by username for public routes.
  - Resolves a cafe by pairing code for public routes.
  - Updates `menu_updated_at`.

- `ActivationService`
  - Resolves the centralized activation URL from the singleton `admin` row.
  - Returns `#` when the admin table is missing or the stored value is blank.
  - Decides whether the shared admin activation banner should render for the current cafe.

- `MenuBuilderService`
  - Resolves a cafe by username or pairing code.
  - Resolves enabled cafe languages and default language.
  - Fetches active categories and public menu items only when cafe status is `active` or `demo`.
  - Builds the multilingual JSON structure consumed by the public UI and external clients, including `meta.languages[*].locale`, top-level `ui_translations`, `cafe.status`, and database-backed `cafe.activation_url`.

- `AdminLanguageService`
  - Resolves the admin UI language from session, cookie, browser `Accept-Language`, then English fallback.
  - Persists explicit admin language changes to both session and cookie.
  - Sanitizes post-switch redirect targets so language changes return to the same internal admin/auth page safely.

- `AdminUiTextCatalogService`
  - Exposes the server-rendered admin/auth translation catalog for every supported language.
  - Falls back to English when a key is missing from the selected language override.
  - Provides translated flash/error text for controllers and services in addition to view labels.

- `PublicUiTextCatalogService`
  - Exposes the fixed public-menu UI text catalog keyed by language code.
  - Returns only the enabled cafe languages for the JSON `ui_translations` payload.
  - Supports English fallback labels for the public shell.

- `CafeLanguageService`
  - Exposes the fixed language catalog.
  - Validates cafe language selection.
  - Persists ordered `cafe_languages` rows.

- `CafeFeeTranslationService`
  - Persists cart-fee labels for the currently enabled cafe languages.
  - Requires a default-language label only when the extra fee is enabled.

- `CategoryService` / `MenuItemService`
  - Save base records and translation records transactionally.

- `FileUploadService`
  - Validates uploaded image MIME types.
  - Stores files under `public/uploads/{username}/`.
  - Resizes non-SVG images if their largest dimension exceeds 1200 pixels.

### Models

- `AdminModel`
  - Encapsulates the singleton `admin` table row used for superadmin credentials and global links/settings.

- `CafeModel`
  - Encapsulates the `cafes` table.
  - Provides general cafe lookup by username/code plus `findRecentActive()`.
  - Validates username, phone, status, theme, URL fields, and related profile data.

- `CafeFeeTranslationModel`
  - Encapsulates the `cafe_fee_translations` table.
  - Provides cafe-scoped access to translated cart-fee labels.

- `CategoryModel`
  - Encapsulates the `categories` table.
  - Provides tenant-scoped lookup and default-language joins through `getByCafe()` and `findByCafe()`.

- `MenuItemModel`
  - Encapsulates the `menu_items` table.
  - Provides admin listing via `getByCafe()` with default-language joins.
  - Provides public filtered listing via `getPublicItemsByCafe()`.

- `CafeLanguageModel`, `CategoryTranslationModel`, `MenuItemTranslationModel`
  - Encapsulate the translation tables and language assignments.

### Filters

- `AdminAuthFilter`
  - Allows requests only when session contains `cafe_id`.
  - Redirects guests to `/login`.

- `SuperAdminAuthFilter`
  - Allows requests only when session contains `superadmin_id`.
  - Redirects guests to `/superadmin/login`.

- `MenuJsonThrottleFilter`
  - Applies to `/{username}/menu.json`, `/{username}/menu`, and `/code/{6-digit-code}`.
  - Throttles by tenant identifier and client IP using CodeIgniter throttler service.
  - Returns HTTP `429` JSON when rate limit is exceeded.

### Helpers

`BaseController` loads the `menu` helper for all controllers.

- `menu_asset_url(?string $path): ?string`
  - Converts a stored relative path such as `uploads/daryo/logo.png` into an absolute public URL.

- `menu_old(string $key, mixed $default = ''): mixed`
  - Wraps CodeIgniter `old()` with a default value for form rendering.

- `menu_old_translation(string $languageCode, string $field, mixed $default = ''): mixed`
  - Reads old translation form values by locale and field.

- `menu_translation_value(array $translations, string $languageCode, string $defaultLanguage, string $field): ?string`
  - Resolves selected-locale text with fallback to the default language.

- `admin_ui(string $key, array $replacements = []): string`
  - Resolves an admin/auth UI translation using the current persisted admin language with English fallback.

- `admin_ui_current_language(): array`
  - Returns the current admin language metadata shared with admin/auth layouts.

- `admin_ui_supported_languages(): array`
  - Returns the full supported language catalog for the admin language switcher.

## Database Architecture

### Source files

- `database/schema.sql` is the schema source of truth for manual setup.
- `database/database.sql` is a phpMyAdmin export that includes structure, constraints, indexes, auto-increment values, and sample/live-like data.

The app does not currently use CodeIgniter migrations for schema management.

### Tables

- `admin`
- `cafes`
- `categories`
- `menu_items`
- `cafe_languages`
- `cafe_fee_translations`
- `category_translations`
- `menu_item_translations`

#### `admin`

Singleton platform settings and superadmin account table.

Important fields:

- `id`
- `username`
- `password_hash`
- `contact_url`
- `social_page_link`
- `app_link_store_normal`
- `app_link_store_kiosk`
- `app_link_local_normal`
- `app_link_local_kiosk`
- `activation_url`

Behavior:

- Uses row `id = 1`.
- `activation_url` is shown in admin banners, public HTML notices, and public JSON.
- Superadmin password is stored with `password_hash()`.

#### `cafes`

Represents a single tenant account.

Important fields:

- `id`
- `code`
- `username`
- `phone`
- `person_name`
- `cafe_name`
- `slogan`
- `password_hash`
- `logo_path`
- `currency_name`
- `theme_style`
- `address_text`
- `location_url`
- `menu_updated_at`
- `status`
- `created_at`

`currency_name` is stored as a short display code/name up to 6 characters and defaults to `USD`.
- `updated_at`

Behavior:

- `username` is unique.
- `code` is nullable and unique when present.
- `status` controls public visibility and login eligibility.
- `status` supports `active`, `demo`, and `inactive`.
- `admin.activation_url` is the single source of truth for activation/payment links shown in admin, public HTML, and public JSON.
- `menu_updated_at` is the menu freshness field for public consumers.

#### `categories`

Represents a menu category owned by a cafe.

Important fields:

- `id`
- `cafe_id`
- `sort_order`
- `is_active`
- `created_at`
- `updated_at`

Behavior:

- Belongs to one cafe through `cafe_id`.
- Ordered first by `sort_order`, then by `id`.
- Active state affects public menu visibility.

#### `menu_items`

Represents a menu item owned by a cafe.

Important fields:

- `id`
- `cafe_id`
- `category_id`
- `price`
- `image_path`
- `is_available`
- `sort_order`
- `created_at`
- `updated_at`

Behavior:

- Belongs to one cafe through `cafe_id`.
- May be uncategorized because `category_id` is nullable.
- Public visibility depends on both `is_available` and category state.

#### `cafe_languages`

Stores the enabled menu languages for a cafe in display order.

Important fields:

- `id`
- `cafe_id`
- `language_code`
- `sort_order`

Behavior:

- `sort_order = 1` is the cafe default language.
- A cafe can have up to 3 rows, enforced in application logic.
- `language_code` values come from the fixed app language catalog.
- Newly registered cafes use the configured default language from the catalog service, currently `en`.

#### `category_translations`

Stores localized category names.

Important fields:

- `id`
- `category_id`
- `language_code`
- `name`

Behavior:

- One row per `(category_id, language_code)`.
- Deleted automatically when the parent category is deleted.

#### `cafe_fee_translations`

Stores localized extra-fee labels for a cafe.

Important fields:

- `id`
- `cafe_id`
- `language_code`
- `label`

Behavior:

- One row per `(cafe_id, language_code)`.
- Used for the public `cafe.extra_fee.translations` payload.
- Deleted automatically when the parent cafe is deleted.

#### `menu_item_translations`

Stores localized menu item names and descriptions.

Important fields:

- `id`
- `menu_item_id`
- `language_code`
- `name`
- `description`

Behavior:

- One row per `(menu_item_id, language_code)`.
- Deleted automatically when the parent menu item is deleted.

### Relationships and deletion rules

- `categories.cafe_id` -> `cafes.id` with `ON DELETE CASCADE`
- `menu_items.cafe_id` -> `cafes.id` with `ON DELETE CASCADE`
- `menu_items.category_id` -> `categories.id` with `ON DELETE SET NULL`
- `cafe_languages.cafe_id` -> `cafes.id` with `ON DELETE CASCADE`
- `cafe_fee_translations.cafe_id` -> `cafes.id` with `ON DELETE CASCADE`
- `category_translations.category_id` -> `categories.id` with `ON DELETE CASCADE`
- `menu_item_translations.menu_item_id` -> `menu_items.id` with `ON DELETE CASCADE`

Effects:

- Deleting a cafe deletes its categories and menu items.
- Deleting a category does not delete menu items; affected items become uncategorized.

### Freshness Tracking

`CafeService::touchMenuUpdatedAt()` updates:

- `cafes.menu_updated_at` to the current application time

This is called after successful changes to:

- cafe settings
- categories
- menu items
- cafe language configuration
- category translations
- menu item translations

## Public JSON Contract

The public JSON is built by `MenuBuilderService` and returned by `MenuJsonController`.

Response shape:

```json
{
  "meta": {
    "username": "bestcafe",
    "updated_at": "2026-04-02T14:30:00+05:00",
    "default_language": "en",
    "languages": [
      {
        "code": "en",
        "label": "English",
        "native_label": "English",
        "dir": "ltr",
        "flag": "🇬🇧",
        "locale": "en-GB"
      },
      {
        "code": "uz",
        "label": "Uzbek",
        "native_label": "O'zbekcha",
        "dir": "ltr",
        "flag": "🇺🇿",
        "locale": "uz-UZ"
      }
    ]
  },
  "cafe": {
    "name": "Best Cafe",
    "status": "active",
    "slogan": "Fresh coffee every day",
    "logo_url": "http://example.com/uploads/bestcafe/logo.jpg",
    "currency": "UZS",
    "theme_style": "theme2",
    "address": "Navoi street 12",
    "location_url": "https://maps.google.com/?q=41.55,60.63",
    "activation_url": "https://t.me/cafemenu_uz?start=pay",
    "extra_fee": {
      "enabled": true,
      "type": "percent",
      "value": 5,
      "translations": {
        "en": {
          "label": "Service fee"
        },
        "uz": {
          "label": "Xizmat haqi"
        }
      }
    }
  },
  "categories": [
    {
      "id": 1,
      "sort_order": 1,
      "icon_url": "http://example.com/uploads/bestcafe/drinks.svg",
      "translations": {
        "en": {
          "name": "Drinks"
        },
        "uz": {
          "name": "Ichimliklar"
        }
      }
    }
  ],
  "ui_translations": {
    "en": {
      "menu_language_label": "Menu language",
      "updated_at": "Updated",
      "all_items": "All items",
      "cart_bar_selected_count": "Selected items: {count}",
      "extra_fee_default_label": "Extra fee",
      "uncategorized": "Others"
    },
    "uz": {
      "menu_language_label": "Menyu tili",
      "updated_at": "Yangilandi",
      "all_items": "Barcha taomlar",
      "cart_bar_selected_count": "Tanlanganlar: {count}",
      "extra_fee_default_label": "Qo'shimcha to'lov",
      "uncategorized": "Boshqalar"
    }
  },
  "items": [
    {
      "id": 11,
      "category_id": 1,
      "price": 18000,
      "image_url": "http://example.com/uploads/bestcafe/cappuccino.jpg",
      "is_available": true,
      "sort_order": 1,
      "translations": {
        "en": {
          "name": "Cappuccino",
          "description": "Hot coffee"
        },
        "uz": {
          "name": "Kapuchino",
          "description": "Issiq qahva"
        }
      }
    }
  ]
}
```

Notes:

- Scalar `categories[].name`, `items[].name`, and `items[].description` are no longer emitted.
- Clients must resolve visible text from `translations`, using `meta.default_language` as fallback.
- `meta.languages` is ordered by cafe language `sort_order`.
- `meta.languages` entries currently include `code`, `label`, `native_label`, `dir`, `flag`, and `locale`.
- `ui_translations` contains public-shell labels for the same enabled cafe languages.
- Clients resolve UI labels from `ui_translations` using selected language, then `meta.default_language`, then English fallback.
- `dir` is included so clients can switch content direction for RTL languages like Arabic.
- `meta.version` is not emitted.

### Filtering rules

Public output is intentionally narrower than admin output:

- `active` and `demo` cafes return the public menu payload.
- `inactive` cafes return the same envelope with `cafe.status = inactive`, `cafe.activation_url` from `admin.activation_url`, and empty `categories` / `items`.
- Categories must be active to appear in `categories`.
- Items must have `is_available = 1`.
- Uncategorized items are allowed when `category_id` is `NULL`.
- Items attached to inactive or missing categories are excluded unless they are uncategorized.

### Response headers and caching

`MenuJsonController` sets:

- `Cache-Control: public, max-age=60`
- `Last-Modified` from `meta.updated_at`

`MenuJsonThrottleFilter` may return:

```json
{
  "error": "Too many requests. Please retry shortly."
}
```

with HTTP status `429`.

## State, Files, and Assets

### Session state

The session is the only authentication state currently used. Important keys:

- `cafe_id`
- `username`

### Uploaded files

Uploads are stored in:

```text
public/uploads/{username}/
```

Stored database values are relative paths such as:

```text
uploads/daryo/1776140379_8d3334629ed53d3686b9.png
```

Accepted upload MIME types:

- `image/jpeg`
- `image/png`
- `image/webp`
- `image/svg+xml`

Non-SVG uploads are resized when needed so the largest dimension is at most 1200 pixels.

### Public frontend assets

The public shell depends on:

- `public/app.js`
- `public/style.css`
- `public/vue.global.js`
- `public/placeholder.png`

At runtime, the shell injects an English fallback UI-text bundle before the JSON fetch completes. After fetch, `public/app.js` merges that fallback with `ui_translations` from the menu JSON and resolves labels by selected language, then cafe default language, then English.

The shell also loads Fancybox from a CDN for image lightbox behavior.

## Testing

Current application-specific feature coverage is centered in `tests/feature/CafeMenuFlowTest.php`.

That test currently verifies:

- guests are redirected away from protected admin routes
- registration creates a cafe and stores a password hash
- public HTML and JSON respect `active`, `demo`, and `inactive` cafe statuses
- `/{username}/menu.json` returns only active categories and available items when the cafe has a public menu
- public JSON includes expected metadata and image URL formatting

The repository also contains baseline example tests generated by the framework in `tests/unit`, `tests/session`, and `tests/database`.

## Known Caveats / Current Mismatches

- `/{username}/menu` is a JSON alias, even though the product concept suggests it could be a user-facing menu page.
- `app/Views/home_ru.php` is routed as the Russian landing page, but most visible content is still Uzbek.
- Required filters in `app/Config/Filters.php` enable page cache globally. This is an operational concern because it affects all routes unless changed in configuration.
- `Filters.php` also aliases `forcehttps`, but `App::$forceGlobalSecureRequests` is currently `false`, so HTTPS enforcement is not globally active by configuration alone.

## Change Safety Rules

- If the database schema changes, update `database/schema.sql`.
- If the seeded or exported SQL dump is intentionally refreshed, update `database/database.sql`.
- If the public JSON payload changes, update both this document and any relevant tests, especially `tests/feature/CafeMenuFlowTest.php`.
- If new top-level or tenant routes are added, place them before the final `GET /(:segment)` catch-all route.
- If new admin mutations affect public menu output, ensure they still update `menu_updated_at`.
- If upload behavior changes, keep stored path format and public URL generation in sync with `menu_asset_url()` and `FileUploadService`.

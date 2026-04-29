# CafeMenu Architecture

## Overview

CafeMenu is a CodeIgniter 4 application for managing and publishing cafe or restaurant menus. The backend is PHP-based, the database is managed through SQL files instead of CodeIgniter migrations, and the public menu UI is a Vue-powered shell that fetches menu data from a JSON endpoint.

The app has two main surfaces:

- Marketing pages for the product landing experience.
- A tenant-based admin and public menu system for individual cafes.

The multi-tenant model is simple: one cafe account maps to one row in `cafes`, and the cafe `username` serves both as the login identifier and the public URL slug.

Menu content is multilingual per cafe. Each cafe can enable 1 to 3 languages in `cafe_languages`, and only category names, menu item names, and menu item descriptions are translated.

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

All admin routes live under `/admin`.

Unauthenticated routes:

- `GET /admin/register`
- `POST /admin/register`
- `GET /admin/login`
- `POST /admin/login`
- `GET /admin/logout`

Authenticated routes guarded by `adminauth`:

- `GET /admin`
- `GET /admin/settings`
- `POST /admin/settings`
- `POST /admin/settings/password`
- Category CRUD under `/admin/categories`
- Menu item CRUD under `/admin/menu-items`

### Public tenant surface

Each cafe is published under `/{username}`.

- `GET /{username}` -> public HTML shell
- `GET /{username}/menu.json` -> public JSON
- `GET /{username}/menu` -> JSON alias to the same controller
- `GET /{username}/manifest.webmanifest` -> generated PWA manifest
- `GET /{username}/sw.js` -> generated service worker

## Routing Model

Routes are defined in `app/Config/Routes.php`. Auto-routing is disabled in `app/Config/Routing.php`, so only explicitly declared routes are reachable.

Route ordering matters:

1. Marketing routes are declared first.
2. Admin routes are grouped under `/admin`.
3. Tenant PWA and JSON routes are declared next.
4. The final catch-all route is `GET /(:segment)` -> `PublicController::index/$1`.

This means:

- Any future top-level route must be declared before the final `(:segment)` route.
- Any future tenant-specific route must be declared before the catch-all `/{username}` route.
- `/{username}/menu` currently returns JSON, not an HTML page.

## Request Flows

### Registration and login

Registration and login are handled by `AuthController`.

Registration flow:

1. Read POST values for username, phone, person name, cafe name, password, currency, and theme.
2. Validate request data.
3. Hash the password with `password_hash()`.
4. Insert a row into `cafes`.
5. Insert the default cafe language row (`ru`).
6. Regenerate the session.
7. Store `cafe_id` and `username` in session.
8. Redirect to `/admin`.

Login flow:

1. Look up cafe by username.
2. Ensure the cafe exists, is `active`, and the password verifies.
3. Regenerate the session.
4. Store `cafe_id` and `username` in session.
5. Redirect to `/admin`.

Protected admin routes use `AdminAuthFilter`, which redirects unauthenticated users to `/admin/login`.

### Admin CRUD flow

Category and menu item CRUD follow the same tenant pattern:

1. Resolve current cafe from session via `CafeService`.
2. Load or mutate only rows that belong to that cafe.
3. Save non-translatable fields in `categories` / `menu_items`.
4. Save translations in `category_translations` / `menu_item_translations`.
5. On successful create, update, delete, or settings change, call `CafeService::bumpMenuVersion()`.

Ownership enforcement is implemented in code:

- Categories are resolved with `CategoryModel::findByCafe()`.
- Menu items are resolved with `MenuItemModel::findByCafe()`.
- Menu item category assignment is checked to ensure the chosen category belongs to the current cafe.

### Public menu flow

The public menu is split into shell and data:

1. `PublicController::index($username)` resolves an active cafe by username.
2. It renders `app/Views/public/menu_shell.php`.
3. The view exposes `window.MenuAppConfig` containing the JSON URL, service worker URL, PWA scope, placeholder image, and default cafe name.
4. `public/app.js` bootstraps a Vue app.
5. The Vue app fetches `/{username}/menu.json`.
6. The client chooses a menu language from localStorage, browser preference, or the cafe default language.
7. The client renders categories, translated menu items, and a local in-browser selection cart.

The cart is client-side only. There is no ordering, checkout, or server-side cart persistence.

### PWA flow

Per-tenant PWA endpoints are generated dynamically:

- `PwaController::manifest($username)` returns a JSON manifest.
- `PwaController::serviceWorker($username)` returns JavaScript for the service worker.

The public shell registers the service worker from `public/app.js` when supported by the browser.

## Layer Responsibilities

### Controllers

Controllers are thin request handlers.

- `Home` renders landing pages and recent active cafes.
- `AuthController` handles registration, login, and logout.
- `AdminController` loads dashboard data and public URLs for the current cafe.
- `CafeSettingsController` updates cafe profile fields, logo, PWA icon, and password.
- `CategoryController` manages category CRUD for the current cafe.
- `MenuItemController` manages menu item CRUD and image uploads for the current cafe.
- `MenuJsonController` returns the normalized public JSON payload.
- `PublicController` renders the tenant menu shell.
- `PwaController` returns tenant-scoped manifest and service worker responses.

### Services

- `CafeService`
  - Resolves the current cafe from session.
  - Resolves an active cafe by username.
  - Increments `menu_version` and updates `menu_updated_at`.

- `MenuBuilderService`
  - Resolves the active cafe.
  - Resolves enabled cafe languages and default language.
  - Fetches active categories and public menu items.
  - Builds the multilingual JSON structure consumed by the public UI and external clients.

- `CafeLanguageService`
  - Exposes the fixed language catalog.
  - Validates cafe language selection.
  - Persists ordered `cafe_languages` rows.

- `CategoryService` / `MenuItemService`
  - Save base records and translation records transactionally.

- `FileUploadService`
  - Validates uploaded image MIME types.
  - Stores files under `public/uploads/{username}/`.
  - Resizes non-SVG images if their largest dimension exceeds 1200 pixels.

### Models

- `CafeModel`
  - Encapsulates the `cafes` table.
  - Provides `findActiveByUsername()` and `findRecentActive()`.
  - Validates username, phone, status, theme, URL fields, and related profile data.

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
  - Redirects guests to `/admin/login`.

- `MenuJsonThrottleFilter`
  - Applies to `/{username}/menu.json` and `/{username}/menu`.
  - Throttles by username and client IP using CodeIgniter throttler service.
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

## Database Architecture

### Source files

- `database/schema.sql` is the schema source of truth for manual setup.
- `database/database.sql` is a phpMyAdmin export that includes structure, constraints, indexes, auto-increment values, and sample/live-like data.

The app does not currently use CodeIgniter migrations for schema management.

### Tables

- `cafes`
- `categories`
- `menu_items`
- `cafe_languages`
- `category_translations`
- `menu_item_translations`

#### `cafes`

Represents a single tenant account.

Important fields:

- `id`
- `username`
- `phone`
- `person_name`
- `cafe_name`
- `slogan`
- `password_hash`
- `logo_path`
- `pwa_icon_path`
- `currency_name`
- `theme_style`
- `address_text`
- `location_url`
- `menu_version`
- `menu_updated_at`
- `status`
- `created_at`
- `updated_at`

Behavior:

- `username` is unique.
- `status` controls public visibility and login eligibility.
- `menu_version` and `menu_updated_at` are used as menu freshness metadata for public consumers.

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
- `category_translations.category_id` -> `categories.id` with `ON DELETE CASCADE`
- `menu_item_translations.menu_item_id` -> `menu_items.id` with `ON DELETE CASCADE`

Effects:

- Deleting a cafe deletes its categories and menu items.
- Deleting a category does not delete menu items; affected items become uncategorized.

### Versioning

`CafeService::bumpMenuVersion()` updates:

- `cafes.menu_version` by incrementing the current value
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
    "version": 5,
    "updated_at": "2026-04-02T14:30:00+05:00",
    "default_language": "ru",
    "languages": [
      {
        "code": "ru",
        "label": "Russian",
        "native_label": "Русский",
        "dir": "ltr"
      },
      {
        "code": "en",
        "label": "English",
        "native_label": "English",
        "dir": "ltr"
      },
      {
        "code": "ar",
        "label": "Arabic",
        "native_label": "العربية",
        "dir": "rtl"
      }
    ]
  },
  "cafe": {
    "name": "Best Cafe",
    "slogan": "Fresh coffee every day",
    "logo_url": "http://example.com/uploads/bestcafe/logo.jpg",
    "pwa_icon_url": null,
    "currency": "UZS",
    "theme_style": "theme2",
    "address": "Navoi street 12",
    "location_url": "https://maps.google.com/?q=41.55,60.63"
  },
  "categories": [
    {
      "id": 1,
      "sort_order": 1,
      "translations": {
        "ru": {
          "name": "Напитки"
        },
        "en": {
          "name": "Drinks"
        },
        "ar": {
          "name": "المشروبات"
        }
      }
    }
  ],
  "items": [
    {
      "id": 11,
      "category_id": 1,
      "price": 18000,
      "image_url": "http://example.com/uploads/bestcafe/cappuccino.jpg",
      "is_available": true,
      "sort_order": 1,
      "translations": {
        "ru": {
          "name": "Капучино",
          "description": "Горячий кофе"
        },
        "en": {
          "name": "Cappuccino",
          "description": "Hot coffee"
        },
        "ar": {
          "name": "كابتشينو",
          "description": "قهوة ساخنة"
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
- `dir` is included so clients can switch content direction for RTL languages like Arabic.

### Filtering rules

Public output is intentionally narrower than admin output:

- Cafe must be active.
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
- `public/icon-192.png`
- `public/icon-512.png`

The shell also loads Fancybox from a CDN for image lightbox behavior.

## Testing

Current application-specific feature coverage is centered in `tests/feature/CafeMenuFlowTest.php`.

That test currently verifies:

- guests are redirected away from protected admin routes
- registration creates a cafe and stores a password hash
- `/{username}/menu.json` returns only active categories and available items
- public JSON includes expected metadata and image URL formatting

The repository also contains baseline example tests generated by the framework in `tests/unit`, `tests/session`, and `tests/database`.

## Known Caveats / Current Mismatches

- `README.md` is partly a spec/history document and does not fully match the current implementation details.
- `/{username}/menu` is a JSON alias, even though the product concept suggests it could be a user-facing menu page.
- `app/Views/public/service_worker.php` only handles install and activate events. It does not currently cache the shell, images, or fetch responses.
- `PwaController::manifest()` currently points to the global `icon-192.png` and `icon-512.png` assets, not the cafe-specific uploaded `pwa_icon_path`.
- `app/Views/home_ru.php` is routed as the Russian landing page, but most visible content is still Uzbek.
- Required filters in `app/Config/Filters.php` enable page cache globally. This is an operational concern because it affects all routes unless changed in configuration.
- `Filters.php` also aliases `forcehttps`, but `App::$forceGlobalSecureRequests` is currently `false`, so HTTPS enforcement is not globally active by configuration alone.

## Change Safety Rules

- If the database schema changes, update `database/schema.sql`.
- If the seeded or exported SQL dump is intentionally refreshed, update `database/database.sql`.
- If the public JSON payload changes, update both this document and any relevant tests, especially `tests/feature/CafeMenuFlowTest.php`.
- If new top-level or tenant routes are added, place them before the final `GET /(:segment)` catch-all route.
- If new admin mutations affect public menu output, ensure they still bump `menu_version` and `menu_updated_at`.
- If upload behavior changes, keep stored path format and public URL generation in sync with `menu_asset_url()` and `FileUploadService`.

# CafeMenu

CafeMenu is a CodeIgniter 4 project for creating and publishing digital food menus for cafes and restaurants.

The app has two main parts:

- A marketing / landing-page surface.
- A tenant-based admin and public menu system where each cafe has its own `username` slug, public menu URL, and optional 6-digit pairing code for app JSON access.

For a detailed implementation map, read [ARCHITECTURE.md](ARCHITECTURE.md).

## Current Stack

- CodeIgniter 4
- PHP `^8.2`
- MySQL / MariaDB
- Vue-based public menu shell
- Direct SQL schema management via files in `database/`

## Current Runtime Surfaces

### Marketing

- `/` -> landing page
- `/ru` -> alternate landing page
- `/thankyou` -> static thank-you page

### Admin

Authentication:

- `GET /register`
- `POST /register`
- `GET /login`
- `POST /login`
- `GET /logout`

Protected admin area:

- `GET /admin`
- `GET /admin/settings`
- `POST /admin/settings`
- `POST /admin/settings/password`
- Category CRUD under `/admin/categories`
- Menu item CRUD under `/admin/menu-items`

### Public Tenant Endpoints

Each cafe is published under `/{username}`.

- `GET /{username}` -> public menu HTML shell
- `GET /{username}/menu.json` -> public menu JSON
- `GET /{username}/menu` -> JSON alias to the same response
- `GET /code/{6-digit-code}` -> public menu JSON by pairing code
- `GET /{username}/manifest.webmanifest` -> generated PWA manifest
- `GET /{username}/sw.js` -> generated service worker

Important routing note:

- Auto-routing is disabled.
- The final `GET /(:segment)` route is a catch-all for tenant pages, so any future top-level routes must be declared before it.

## Current Product Model

- One cafe account maps to one row in `cafes`.
- `username` is both the login identifier and the public tenant slug.
- `code` is an optional 6-digit pairing code for app JSON access.
- Categories and menu items belong to a cafe through `cafe_id`.
- Each cafe enables `1..3` menu languages through `cafe_languages`.
- Newly registered cafes start with English (`en`) as the default menu language.
- Public menu visibility depends on cafe status, category active state, and item availability.

## Database and Schema

The project currently uses SQL files instead of CodeIgniter migrations.

- `database/schema.sql`
  - Schema source of truth for manual setup.
- `database/database.sql`
  - phpMyAdmin-style export with schema, constraints, indexes, auto-increment values, and sample/live-like data.

Core tables:

- `cafes`
- `categories`
- `menu_items`
- `cafe_languages`
- `cafe_fee_translations`
- `category_translations`
- `menu_item_translations`

Important cafe fields:

- `code`
- `username`
- `status`
- `menu_updated_at`
- `logo_path`
- `pwa_icon_path`
- `extra_fee_enabled`
- `extra_fee_type`
- `extra_fee_value`

Relationship behavior:

- Deleting a cafe cascades to categories and menu items.
- Deleting a category sets `menu_items.category_id` to `NULL`.

## Public JSON Contract

The public JSON is built by `MenuBuilderService` and returned by `MenuJsonController`.

Primary endpoint:

```text
GET /{username}/menu.json
```

Additional app-friendly endpoint:

```text
GET /code/{6-digit-code}
```

Current response shape:

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
        "dir": "ltr"
      },
      {
        "code": "ru",
        "label": "Russian",
        "native_label": "Русский",
        "dir": "ltr"
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
    "location_url": "https://maps.google.com/?q=41.55,60.63",
    "extra_fee": {
      "enabled": true,
      "type": "percent",
      "value": 5,
      "translations": {
        "en": { "label": "Service fee" },
        "ru": { "label": "Сервисный сбор" }
      }
    }
  },
  "categories": [
    {
      "id": 1,
      "sort_order": 1,
      "translations": {
        "en": { "name": "Drinks" },
        "ru": { "name": "Напитки" }
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
        "en": { "name": "Cappuccino", "description": "Hot coffee" },
        "ru": { "name": "Капучино", "description": "Горячий кофе" }
      }
    }
  ]
}
```

Current public filtering rules:

- Cafe must be `active`.
- Categories must be active to appear in the public `categories` array.
- Items must have `is_available = 1`.
- Uncategorized items are allowed.
- Items linked to inactive or missing categories are excluded unless `category_id` is `NULL`.

Current JSON headers:

- `Cache-Control: public, max-age=60`
- `Last-Modified` from `meta.updated_at`

Current public throttling:

- `/{username}/menu.json`, `/{username}/menu`, and `/code/{6-digit-code}` use `MenuJsonThrottleFilter`.
- Throttle failures return HTTP `429` JSON.

## Images and File Storage

Uploads are stored under:

```text
public/uploads/{username}/
```

Only relative paths are saved in the database, for example:

```text
uploads/bestcafe/burger.jpg
```

Current upload behavior:

- Allowed MIME types: JPEG, PNG, WebP, SVG
- Non-SVG images are resized if their largest dimension exceeds `1200px`

## Authentication and Validation

Current authentication behavior:

- Passwords are hashed with `password_hash()`
- Login uses `password_verify()`
- Session stores `cafe_id` and `username`
- Protected admin routes use `AdminAuthFilter`
- Guests are redirected to `/login` when they open protected admin routes

Current password rules in code:

- Registration password minimum length: `5`
- Password change minimum length: `5`

Current username rule:

- Regex: `/^[a-z0-9_-]+$/`
- Registration and login normalize usernames before validation/lookup:
  - trim
  - remove all whitespace
  - lowercase

## Frontend Behavior

The tenant public page is an HTML shell rendered by PHP and hydrated by `public/app.js`.

Current behavior:

- Vue fetches `/{username}/menu.json`
- Vue stores the selected menu language in localStorage per cafe
- Vue switches category and item text client-side with fallback to the cafe default language
- Menu items are grouped by category in the client
- The UI includes a local client-side selection cart with an optional per-cafe extra fee
- The shell registers a tenant-scoped service worker
- Fancybox is loaded from CDN for image previews

Current PWA limitation:

- The service worker currently handles only install / activate lifecycle events
- It does not currently cache shell HTML, images, or fetch responses

Current manifest limitation:

- `manifest.webmanifest` uses global `icon-192.png` and `icon-512.png`
- It does not currently use the uploaded cafe-specific `pwa_icon_path`

## Tests

The main app-specific feature coverage is in:

- `tests/feature/CafeMenuFlowTest.php`

It currently verifies:

- guest redirect on protected admin route
- registration creates a cafe, password hash, default language row, and normalized username handling
- registration can still succeed when pairing code generation falls back to `NULL`
- category translation persistence for enabled cafe languages
- duplicate language rejection in cafe settings
- menu item translation validation
- public JSON returns multilingual metadata and translation maps while keeping existing public filtering
- `/code/{6-digit-code}` JSON access works for cafes that have a pairing code

## Development Notes

- Before changing schema-related backend logic, check `database/schema.sql`.
- If the schema changes, update `database/schema.sql`.
- If the exported SQL snapshot is intentionally refreshed, update `database/database.sql`.
- If the public JSON payload changes, update tests and `ARCHITECTURE.md`.
- If a runtime error appears, check the newest file in `writable/logs/`.

## Known Current Caveats

- `home_ru.php` is routed as the Russian landing page, but much of its visible content is still Uzbek.
- Page cache is enabled as a required filter in `app/Config/Filters.php`, which can affect debugging and response behavior.
- HTTPS force filter is present in aliases and required filters, but `App::$forceGlobalSecureRequests` is currently `false`.

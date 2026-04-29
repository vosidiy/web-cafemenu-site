# CafeMenu

CafeMenu is a CodeIgniter 4 project for creating and publishing digital food menus for cafes and restaurants.

The app has two main parts:

- A marketing / landing-page surface.
- A tenant-based admin and public menu system where each cafe has its own `username` slug and public menu URL.

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

- `GET /admin/register`
- `POST /admin/register`
- `GET /admin/login`
- `POST /admin/login`
- `GET /admin/logout`

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
- `GET /{username}/manifest.webmanifest` -> generated PWA manifest
- `GET /{username}/sw.js` -> generated service worker

Important routing note:

- Auto-routing is disabled.
- The final `GET /(:segment)` route is a catch-all for tenant pages, so any future top-level routes must be declared before it.

## Current Product Model

- One cafe account maps to one row in `cafes`.
- `username` is both the login identifier and the public tenant slug.
- Categories and menu items belong to a cafe through `cafe_id`.
- Each cafe enables `1..3` menu languages through `cafe_languages`.
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
- `category_translations`
- `menu_item_translations`

Important cafe fields:

- `username`
- `status`
- `menu_version`
- `menu_updated_at`
- `logo_path`
- `pwa_icon_path`

Relationship behavior:

- Deleting a cafe cascades to categories and menu items.
- Deleting a category sets `menu_items.category_id` to `NULL`.

## Public JSON Contract

The public JSON is built by `MenuBuilderService` and returned by `MenuJsonController`.

Primary endpoint:

```text
GET /{username}/menu.json
```

Current response shape:

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
        "ru": { "name": "Напитки" },
        "en": { "name": "Drinks" }
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
        "ru": { "name": "Капучино", "description": "Горячий кофе" },
        "en": { "name": "Cappuccino", "description": "Hot coffee" }
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

- `/{username}/menu.json` and `/{username}/menu` use `MenuJsonThrottleFilter`.
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

Current password rules in code:

- Registration password minimum length: `5`
- Password change minimum length: `5`

Current username rule:

- Regex: `/^[a-z0-9_-]+$/`

## Frontend Behavior

The tenant public page is an HTML shell rendered by PHP and hydrated by `public/app.js`.

Current behavior:

- Vue fetches `/{username}/menu.json`
- Vue stores the selected menu language in localStorage per cafe
- Vue switches category and item text client-side with fallback to the cafe default language
- Menu items are grouped by category in the client
- The UI includes a local client-side selection cart
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
- registration creates a cafe, password hash, and default language row
- category translation persistence for enabled cafe languages
- duplicate language rejection in cafe settings
- menu item translation validation
- public JSON returns multilingual metadata and translation maps while keeping existing public filtering

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

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

### Cafe Owner Admin

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
- `POST /admin/settings/extra-fee`
- `POST /admin/settings/password`
- Category CRUD under `/admin/categories`
- Menu item CRUD under `/admin/menu-items`

### Superadmin

Authentication:

- `GET /superadmin/login`
- `POST /superadmin/login`
- `GET /superadmin/logout`

Protected platform admin area:

- `GET /superadmin` -> all cafes table
- `GET /superadmin/settings`
- `POST /superadmin/settings`
- `GET /superadmin/account`
- `POST /superadmin/account`
- `GET /superadmin/cafes/{id}/edit`
- `POST /superadmin/cafes/{id}`
- `POST /superadmin/cafes/{id}/password`

### Public Tenant Endpoints

Each cafe is published under `/{username}`.

- `GET /{username}` -> public menu HTML shell
- `GET /{username}/menu.json` -> public menu JSON
- `GET /{username}/menu` -> JSON alias to the same response
- `GET /code/{6-digit-code}` -> public menu JSON by pairing code

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
- Newly registered cafes may provide an optional `currency_name` up to 6 characters; blank registration uses `USD`.
- Public JSON includes translated menu content plus a `ui_translations` object for those enabled menu languages.
- Newly registered cafes start with status `demo`.
- `admin.activation_url` is the global activation/payment link used across admin, public pages, and JSON.
- Cafe status now supports `active`, `demo`, and `inactive`.
- `active` and `demo` cafes expose the public menu, while `inactive` cafes show an activation page on `/{username}`.
- Public JSON remains available for all known cafes: `active` and `demo` return menu data, while `inactive` returns an inactive envelope with empty `categories` and `items`.
- Admin login is allowed for `active` and `demo` cafes.
- `demo` and `inactive` cafes see an activation banner on all admin pages.
- Public menu visibility still depends on cafe status, category active state, and item availability.

## Database and Schema

The project currently uses SQL files instead of CodeIgniter migrations.

- `database/schema.sql`
  - Schema source of truth for manual setup.
- `database/database.sql`
  - phpMyAdmin-style export with schema, constraints, indexes, auto-increment values, and sample/live-like data.

Core tables:

- `admin`
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
- `currency_name`
- `status`
- `menu_updated_at`
- `logo_path`
- `extra_fee_enabled`
- `extra_fee_type`
- `extra_fee_value`

Relationship behavior:

- The `admin` table is a singleton settings/account table using row `id = 1`.
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
        "en": { "label": "Service fee" },
        "uz": { "label": "Xizmat haqi" }
      }
    }
  },
  "public_status": "active",
  "categories": [
    {
      "id": 1,
      "sort_order": 1,
      "icon_url": "http://example.com/uploads/bestcafe/drinks.svg",
      "translations": {
        "en": { "name": "Drinks" },
        "uz": { "name": "Ichimliklar" }
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
        "en": { "name": "Cappuccino", "description": "Hot coffee" },
        "uz": { "name": "Kapuchino", "description": "Issiq qahva" }
      }
    }
  ]
}
```

Current public filtering rules:

- Cafe status controls the response:
  - `active` and `demo` return the public menu.
  - `inactive` returns the same envelope with `public_status: "inactive"`, `cafe.status: "inactive"`, `cafe.activation_url` from `admin.activation_url`, and empty `categories` / `items`.
- `meta.languages`, `ui_translations`, category/item `translations`, and extra-fee `translations` are scoped to the cafe's enabled menu languages.
- Clients should resolve category/item text from each record's `translations` object using `meta.default_language` as fallback.
- Clients should resolve shell labels from `ui_translations` using selected language, then `meta.default_language`, then English fallback.
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
- Login accepts cafes with status `active` or `demo`
- Session stores `cafe_id` and `username`
- Protected admin routes use `AdminAuthFilter`
- Guests are redirected to `/login` when they open protected admin routes
- Admin pages show a shared activation banner for `demo` and `inactive` cafes using `admin.activation_url`
- Superadmin login uses the singleton `admin` row and stores `superadmin_id` / `superadmin_username` in session
- Protected superadmin routes use `SuperAdminAuthFilter`

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

- The shell links static favicon assets and a static manifest from `/menu-favicon/`
- Vue fetches `/{username}/menu.json`
- Vue stores the selected menu language in localStorage per cafe
- Vue switches category and item text client-side with fallback to the cafe default language
- Vue switches shell labels client-side from `ui_translations`
- Menu items are grouped by category in the client
- The UI includes a local client-side selection cart with an optional per-cafe extra fee
- Fancybox is loaded from CDN for image previews
- There is no tenant-specific manifest or service-worker route

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
- public HTML and JSON respect `active`, `demo`, and `inactive` cafe states
- `/code/{6-digit-code}` JSON access works for demo cafes and returns an inactive envelope for inactive cafes

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

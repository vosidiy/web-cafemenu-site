<?php
$pageTitle = 'CafeMenu - Электронное меню для ресторанов, кафе и QR-меню';
$pageDescription = 'CafeMenu - это программа электронного меню для ресторанов, кафе, баров и фудкортов. Запускайте меню на планшетах, QR-меню, мультиязычные меню и управляйте ими из одной админ-панели.';
$canonicalUrl = site_url('ru');
$englishUrl = base_url('/');
$ogImage = base_url('img/intro.png');
$contactUrl = 'https://t.me/vosidiy?text=Здравствуйте, мне нужна помощь по CafeMenu';
$faqItems = [
    [
        'question' => 'Что такое CafeMenu?',
        'answer' => 'CafeMenu - это программа электронного меню для ресторанов, кафе, баров и фудкортов. Она помогает публиковать меню на планшетах, QR-страницах и гостевых экранах из одной админ-панели.',
    ],
    [
        'question' => 'Кому подходит CafeMenu?',
        'answer' => 'CafeMenu подходит ресторанам, кафе, кофейням, барам, столовым и заведениям самообслуживания, которым важно быстро обновлять меню и удобнее показывать его гостям.',
    ],
    [
        'question' => 'Как работает CafeMenu?',
        'answer' => 'Вы создаёте меню в админ-панели, устанавливаете приложение на планшет или используете QR-код для смартфонов гостей. Все обновления меню управляются из одного места.',
    ],
    [
        'question' => 'Можно ли работать без постоянного интернета?',
        'answer' => 'Да. После первой синхронизации приложение может хранить данные меню на устройстве, поэтому меню остаётся доступным даже при нестабильном интернете.',
    ],
    [
        'question' => 'Поддерживает ли CafeMenu несколько языков?',
        'answer' => 'Да. CafeMenu поддерживает мультиязычное меню, что помогает заведению обслуживать местных гостей и иностранных посетителей.',
    ],
];
$softwareSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => 'CafeMenu',
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Android, Web',
    'url' => $canonicalUrl,
    'description' => $pageDescription,
];
$organizationSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'CafeMenu',
    'url' => $englishUrl,
    'logo' => base_url('icon-512.png'),
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'contactType' => 'sales',
        'url' => $contactUrl,
        'availableLanguage' => ['ru', 'en', 'uz'],
    ],
];
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static fn (array $item): array => [
        '@type' => 'Question',
        'name' => $item['question'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['answer'],
        ],
    ], $faqItems),
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?></title>
    <meta name="description" content="<?= esc($pageDescription) ?>">
    <link rel="canonical" href="<?= esc($canonicalUrl) ?>">
    <link rel="alternate" hreflang="ru" href="<?= esc($canonicalUrl) ?>">
    <link rel="alternate" hreflang="en" href="<?= esc($englishUrl) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= esc($englishUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= esc($pageTitle) ?>">
    <meta property="og:description" content="<?= esc($pageDescription) ?>">
    <meta property="og:url" content="<?= esc($canonicalUrl) ?>">
    <meta property="og:site_name" content="CafeMenu">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:image" content="<?= esc($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($pageTitle) ?>">
    <meta name="twitter:description" content="<?= esc($pageDescription) ?>">
    <meta name="twitter:image" content="<?= esc($ogImage) ?>">
    <link href="final.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <script type="application/ld+json"><?= json_encode($softwareSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>
    <script type="application/ld+json"><?= json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>
    <script type="application/ld+json"><?= json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>
</head>
<body>

<header id="header" class="border-bottom py-2 bg-white">
    <div class="container md:d-flex md:flex-row md:justify-content-between md:align-items-center">
        <div class="d-flex align-items-center">
            <a href="<?= site_url('ru'); ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
                <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36" fill="#b85000"><path d="M226.67-500v-234h234v234h-234Zm0 274v-234h234v234h-234ZM500-500v-234h234v234H500Zm0 274v-234h234v234H500ZM186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586.66q0 27-19.83 46.84Q800.33-120 773.33-120H186.67Zm0-66.67h586.66v-586.66H186.67v586.66Z"/></svg>
                <div style="font-size:24px;" class="text-orange-800 font-semibold ml-1">Cafe<span class="text-secondary">Menu</span></div>
            </a>
            <div class="md:d-none ml-auto">
                <a href="<?= esc($contactUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-icon btn-default" aria-label="Связаться с CafeMenu в Telegram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-icon lucide-phone"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                </a>
                <a href="<?= site_url('login') ?>" class="btn btn-orange">Войти</a>
                <button class="btn btn-neutral ml-1 btn-icon" onclick="toggleMenu()" aria-label="Открыть меню навигации">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                </button>
            </div>
        </div>

        <nav id="menu_header" class="d-none md:d-block" aria-label="Основная навигация">
            <ul class="nav nav-col md:flex-row mt-4 md:mt-0">
                <li><a href="#header" class="nav-link">Главная</a></li>
                <li><a href="#what-is" class="nav-link">Что такое CafeMenu?</a></li>
                <li><a href="#how-it-works" class="nav-link">Как это работает</a></li>
                <li><a href="#faq" class="nav-link">FAQ</a></li>
                <li><a href="#contact" class="nav-link">Контакты</a></li>
                <li><a href="<?= esc($englishUrl) ?>" class="nav-link">🇺🇿 English</a></li>
            </ul>
        </nav>

        <div class="d-none md:d-block">
            <a href="#contact" class="btn btn-default">Связаться</a>
            <a href="<?= site_url('login') ?>" class="btn btn-orange">Вход в админ</a>
        </div>
    </div>
</header>

<section class="pt-4">
    <div class="container">
        <article class="bg-orange-100 p-5 lg:p-10" style="border-radius:24px;">
            <div class="row align-items-center">
                <main class="md:col-7 md:p-6">
                    <p class="text-orange font-semibold mb-3">Электронное меню для ресторанов и кафе</p>
                    <h1 class="font-bold text-4xl mt-3 lg:text-6xl mb-4">Запускайте меню на планшетах, QR-меню и мультиязычные меню из одной админ-панели</h1>
                    <p class="text-xl mb-6">
                        CafeMenu помогает ресторанам, кафе, барам и фудкортам показывать меню на
                        <strong class="text-orange">планшетах, смартфонах, киосках и мониторах</strong>.
                        Обновляйте блюда, цены, акции и гостевой контент быстро и без ручного редактирования на каждом устройстве.
                    </p>
                    <p class="text-lg text-secondary mb-8">
                        Это практичная e-menu платформа для заведений, которым важно быстрее обновлять меню, удобнее показывать его гостям и поддерживать несколько языков.
                    </p>
                    <div class="mb-4 d-flex flex-col sm:flex-row gap-2">
                        <a href="#contact" class="btn btn-lg btn-orange">Запросить демо</a>
                        <a href="#how-it-works" class="btn btn-lg btn-default border-orange">Посмотреть как это работает</a>
                    </div>
                </main>
                <aside class="md:col-5">
                    <img width="578" src="img/intro.png" alt="Интерфейс CafeMenu на планшете с категориями блюд и фотографиями меню ресторана">
                </aside>
            </div>
        </article>
    </div>
</section>

<section class="py-10" id="what-is">
    <div class="container">
        <div class="row">
            <div class="md:col-7">
                <h2 class="text-3xl lg:text-5xl mb-4">Что такое CafeMenu?</h2>
                <p class="text-lg mb-4">
                    CafeMenu - это система электронного меню для заведений общественного питания, которым нужен более быстрый способ управления меню на планшетах, QR-страницах и гостевых экранах.
                </p>
                <p class="text-lg mb-4">
                    Вместо ручного редактирования меню на разных устройствах команда работает с одним источником данных и публикует изменения на все нужные цифровые поверхности.
                </p>
                <p class="text-lg text-secondary">
                    Особенно полезно для заведений с частой сменой цен, сезонными блюдами, акциями и гостями, которым нужен доступ к меню на нескольких языках.
                </p>
            </div>
            <div class="md:col-5">
                <article class="card bg-neutral-100 border-0 rounded-lg p-5 h-full">
                    <h3 class="text-2xl mb-3">Кому подходит CafeMenu?</h3>
                    <ul class="list-check d-flex flex-col gap-3">
                        <li>Ресторанам, которым важно быстро обновлять меню и цены</li>
                        <li>Кафе и кофейням, использующим QR-меню на столах</li>
                        <li>Барам и лаунжам, продвигающим спецпредложения</li>
                        <li>Столовым и фудкортам с большим количеством позиций</li>
                        <li>Заведениям, обслуживающим местных и иностранных гостей</li>
                    </ul>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="pb-10 pt-18" id="features">
    <div class="container">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Основные возможности</h2>
            <p class="text-lg text-secondary">Все, что нужно для публикации, обновления и показа современного ресторанного меню на гостевых устройствах</p>
        </header>

        <div class="row row-cols-1 gap-rows-3 lg:row-cols-3">
            <div class="col">
                <article class="card border-0 rounded-lg bg-red-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-1.png" height="200" alt="Android-планшет с приложением CafeMenu в ресторане" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="my-2">Приложение для планшета</h3>
                    <p>Установите меню на Android-планшеты и дайте гостям визуально удобный способ выбора блюд.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border-0 rounded-lg bg-purple-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-2.png" height="200" alt="Гость сканирует QR-код, чтобы открыть меню ресторана на телефоне" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="my-2">QR-меню</h3>
                    <p>Разместите QR-код на столе, чтобы гости открывали меню на своих смартфонах без ожидания.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border-0 rounded-lg bg-blue-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-3.png" height="200" alt="Цифровой экран меню с категориями блюд и фотографиями" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="my-2">Готовность к офлайн-работе</h3>
                    <p>Приложение может хранить меню на устройстве, что полезно для заведений с нестабильным интернетом.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-10" id="how-it-works">
    <div class="container">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Как работает CafeMenu?</h2>
            <p class="text-lg text-secondary">Настройка простая: создайте меню один раз, опубликуйте его на устройствах и управляйте обновлениями из одного места.</p>
        </header>
        <div class="row row-cols-1 gap-rows-3 lg:row-cols-4">
            <div class="col">
                <article class="card border rounded-lg p-5 h-full">
                    <h3 class="text-2xl mb-3">1. Создайте меню</h3>
                    <p>Добавьте категории, блюда, описания, фото и цены в админ-панели.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border rounded-lg p-5 h-full">
                    <h3 class="text-2xl mb-3">2. Выберите формат показа</h3>
                    <p>Используйте меню на планшете, киоск, QR-меню или гостевой монитор в зависимости от формата обслуживания.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border rounded-lg p-5 h-full">
                    <h3 class="text-2xl mb-3">3. Покажите меню гостям</h3>
                    <p>Гости просматривают блюда, увеличивают фото, сравнивают позиции и делают более уверенный выбор.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border rounded-lg p-5 h-full">
                    <h3 class="text-2xl mb-3">4. Обновляйте в любое время</h3>
                    <p>Меняйте цены, наличие и акции один раз и синхронизируйте обновления на всех цифровых поверхностях.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-10">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <aside class="md:col-6">
                <div class="rounded-lg bg-primary-200 mb-4">
                    <img class="w-full h-auto rounded-lg" height="400" src="img/feature-4.png" alt="Админ-панель CafeMenu на ноутбуке для обновления ресторанного меню">
                </div>
            </aside>
            <aside class="md:col-6">
                <article class="md:ml-6">
                    <h2 class="text-3xl lg:text-5xl mb-3">Управляйте всеми устройствами через одну админ-панель</h2>
                    <hr>
                    <p class="mb-4">
                        Если у вас несколько планшетов или цифровых экранов, CafeMenu позволяет управлять ими из одного места вместо ручного обновления каждого устройства.
                    </p>
                    <ul class="list-check mt-4 mb-4">
                        <li>Простой интерфейс для работы с меню</li>
                        <li>Быстрое добавление новых блюд и категорий</li>
                        <li>Редактирование описаний, цен и наличия</li>
                        <li>Обновление меню на подключенных устройствах</li>
                    </ul>
                </article>
            </aside>
        </div>
    </div>
</section>

<section class="py-10">
    <div class="container">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">QR-меню или меню на планшете?</h2>
            <p class="text-lg text-secondary">CafeMenu поддерживает оба формата. Многие заведения используют их вместе для разных сценариев гостевого опыта.</p>
        </header>
        <div class="row row-cols-1 gap-rows-3 lg:row-cols-2">
            <div class="col">
                <article class="card bg-orange-100 border-0 rounded-lg p-6 h-full">
                    <h3 class="text-3xl mb-3">QR-меню</h3>
                    <p class="mb-3">Подходит для столов, fast casual форматов и гостей, которые предпочитают использовать собственный телефон.</p>
                    <ul class="list-check d-flex flex-col gap-3">
                        <li>Не нужен общий планшет</li>
                        <li>Быстрый доступ по QR-коду на столе</li>
                        <li>Удобно для лёгкого самообслуживания</li>
                    </ul>
                </article>
            </div>
            <div class="col">
                <article class="card bg-neutral-100 border-0 rounded-lg p-6 h-full">
                    <h3 class="text-3xl mb-3">Меню на планшете</h3>
                    <p class="mb-3">Подходит для более наглядной подачи блюд и заведений, которые хотят дать гостям более премиальный опыт за столом.</p>
                    <ul class="list-check d-flex flex-col gap-3">
                        <li>Крупные фото блюд и лучшая презентация</li>
                        <li>Удобный просмотр большого количества позиций</li>
                        <li>Хорошо работает в офлайн-сценариях</li>
                    </ul>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-10">
    <div class="container">
        <div class="position-relative overflow-hidden bg-orange-900 rounded-lg" style="background-image:url(img/bg-about.png)">
            <article class="p-6 lg:p-10" style="background-color:rgba(0,0,0,0.6); max-width:720px">
                <h2 class="mb-2 text-white text-2xl lg:text-5xl">Чем CafeMenu отличается?</h2>
                <ul class="d-flex flex-col gap-4 my-5 text-white list-bullet">
                    <li>
                        <h3 class="text-xl mb-2">Быстрые обновления на всех устройствах</h3>
                        <p>Когда появляется новое блюдо или меняется цена, команда обновляет меню централизованно, а не редактирует каждое устройство вручную.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Более полезная подача блюд</h3>
                        <p>Гости видят фотографии, читают описания, сравнивают категории и легче выбирают нужные позиции.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Поддержка иностранных гостей</h3>
                        <p>Мультиязычное меню помогает ресторанам обслуживать туристов и иностранных посетителей без лишней путаницы.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Один продукт для разных цифровых поверхностей</h3>
                        <p>Используйте одну и ту же базу меню для планшетов, смартфонов гостей, киосков и экранов вместо нескольких разрозненных систем.</p>
                    </li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="py-10">
    <div class="container">
        <div class="row row-cols-1 gap-rows-3 lg:row-cols-2">
            <div class="col">
                <article class="card border rounded-lg p-6 h-full">
                    <h2 class="text-3xl mb-4">Поддерживаемые устройства и языки</h2>
                    <p class="mb-4">CafeMenu рассчитан на ресторанные меню на планшетах, доступ с гостевых смартфонов по QR-коду и общие цифровые экраны.</p>
                    <ul class="list-check d-flex flex-col gap-3">
                        <li>Android-планшеты для цифрового меню в зале</li>
                        <li>Смартфоны гостей для QR-доступа</li>
                        <li>Киоски и мониторы для самообслуживания или показа</li>
                        <li>Мультиязычное меню для местных и иностранных гостей</li>
                    </ul>
                </article>
            </div>
            <div class="col">
                <article class="card border rounded-lg p-6 h-full">
                    <h2 class="text-3xl mb-4">Типовой запуск</h2>
                    <p class="mb-4">Запуск устроен просто и подходит командам, которым нужен практичный цифровой переход без длинного IT-проекта.</p>
                    <ul class="list-check d-flex flex-col gap-3">
                        <li>Создание аккаунта кафе и структуры меню</li>
                        <li>Загрузка блюд, фото и переводов</li>
                        <li>Установка приложения на планшет или печать QR-кодов</li>
                        <li>Проверка гостевого сценария и запуск</li>
                    </ul>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-10" id="restaurants">
    <div class="container">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Примеры активных заведений</h2>
            <p class="text-lg text-secondary">Недавно активные кафе показывают, что CafeMenu используется как реальный продукт электронного меню.</p>
        </header>
        <div class="row">
            <div class="md:col-8 mx-auto">
                <article class="card bg-neutral-100 border-0 rounded-lg p-6 text-center">
                    <?php if (! empty($recentCafes)): ?>
                        <p class="mb-4">Примеры недавних активных кафе:</p>
                        <p class="text-lg mb-5">
                            <?= esc(implode(', ', array_map(static fn (array $recentCafe): string => $recentCafe['cafe_name'] ?: $recentCafe['username'], $recentCafes))) ?>
                        </p>
                    <?php else: ?>
                        <p class="mb-5">Примеры заведений можно показать во время демонстрации продукта.</p>
                    <?php endif; ?>
                    <div class="d-flex flex-col sm:flex-row gap-2 justify-content-center">
                        <a href="#contact" class="btn btn-orange">Запросить демо</a>
                        <a href="#" onclick="open_dialog('dialog_cafe')" class="btn btn-default">Открыть примеры</a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-10" id="faq">
    <div class="container" style="max-width:960px;">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Частые вопросы</h2>
            <p class="text-lg text-secondary">Короткие ответы, которые помогают понять продукт и лучше читаются поисковыми системами и AI-чатами.</p>
        </header>
        <div class="d-grid gap-2">
            <?php foreach ($faqItems as $item): ?>
                <article class="card border rounded-lg p-5 mb-3">
                    <h3 class="text-2xl mb-3"><?= esc($item['question']) ?></h3>
                    <p><?= esc($item['answer']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-10" id="contact">
    <div class="container">
        <div class="row">
            <div class="md:col-6 mx-auto">
                <article class="card shadow-lg bg-neutral-100 border-orange border-width-3 md:p-8 p-4 rounded-lg">
                    <h2 class="mb-3 text-2xl lg:text-3xl">Запросить демо CafeMenu</h2>
                    <p class="mb-5 text-secondary">Расскажите о вашем кафе, ресторане или баре, и мы свяжемся с вами, чтобы показать демо и обсудить внедрение.</p>
                    <form action="https://api.web3forms.com/submit" method="POST">
                        <input type="hidden" name="access_key" value="df365b70-5ae9-4729-a504-ef3cba73313a">
                        <input type="hidden" name="redirect" value="<?= base_url(); ?>thankyou">
                        <input type="hidden" name="subject" value="Cafemenu Message from Website RU">

                        <div class="mb-4">
                            <label class="form-label font-medium">Название кафе</label>
                            <input name="cafe" required type="text" class="form-control text-lg" placeholder="Название бренда или заведения">
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-medium">Ваш номер телефона</label>
                            <input name="phone" required type="tel" class="form-control text-lg" value="+998">
                        </div>
                        <div class="text-secondary">
                            Мы свяжемся с вами в течение 1-2 часов
                        </div>
                        <div class="mt-5 gap-2 d-flex">
                            <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg w-full">Отправить заявку</button>
                        </div>
                    </form>

                    <hr>
                    <p class="text-muted text-center mb-4">ИЛИ</p>
                    <a href="<?= esc($contactUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-lg btn-default w-full">Написать в Telegram</a>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-10">
    <div class="container" style="max-width:900px;">
        <header class="text-center mb-10 mx-auto">
            <h2 class="md:text-3xl mb-4">Приложения для планшета и смартфона</h2>
            <p class="text-lg text-secondary">Установите приложение и введите username, созданный для вашего кафе. После первой синхронизации меню сохраняется в приложении. Интернет нужен для первой загрузки и последующих обновлений.</p>
        </header>

        <article class="card bg-secondary card-body">
            <div class="d-flex align-items-center gap-2">
                <h3 class="">APK-файл</h3>
            </div>
            <hr>
            <nav class="d-grid sm:grid-template-cols-2 gap-2">
                <a href="https://expo.dev/accounts/vosidiy/projects/cafe-menu-tablet/builds/90c214db-1f98-4c06-a304-f172cadec9c0" class="border d-block hover:border-primary rounded p-3 bg-base shadow" target="_blank" rel="noopener noreferrer">
                    <b>Cafe menu standard (expo.dev)</b> <br> Download
                </a>
                <a href="https://expo.dev/artifacts/eas/iSLFvsf6GKksPZdmLiyFmL.apk" class="border d-block hover:border-primary rounded p-3 bg-base shadow" target="_blank" rel="noopener noreferrer">
                    <b>Cafe menu standard (.apk)</b> <br> Download
                </a>
            </nav>
        </article>
        <br><br>
    </div>
</section>

<footer class="border-top bg-orange-100 py-12">
    <div class="container">
        <section class="d-flex align-items-center flex-col lg:flex-row lg:justify-content-between">
            <p class="lg:mb-0 mb-3 text-center lg:text-left"><b>Cafe Menu ©</b><br>cafemenu.uz - программа электронного меню для ресторанов, кафе и QR-меню</p>
            <nav class="nav">
                <a href="#header" class="nav-link">Главная</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <a href="#contact" class="nav-link">Контакты</a>
            </nav>
        </section>
    </div>
</footer>

<dialog class="dialog mt-8" id="form_request">
    <header class="dialog-header">
        <h5>Запросить демо</h5>
        <button class="btn btn-icon" onclick="close_dialog(this)">
            <svg viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
                <path d="M19.0005 4.99988L5.00049 18.9999M5.00049 4.99988L19.0005 18.9999" stroke="currentColor" stroke-width="2"></path>
            </svg>
        </button>
    </header>
    <div class="dialog-body">
        <form action="https://api.web3forms.com/submit" method="POST">
            <input type="hidden" name="access_key" value="df365b70-5ae9-4729-a504-ef3cba73313a">
            <input type="hidden" name="redirect" value="<?= base_url(); ?>thankyou">
            <input type="hidden" name="subject" value="Cafemenu Message from Website RU">

            <div class="mb-4">
                <label class="form-label font-medium">Название кафе</label>
                <input name="cafe" required type="text" class="form-control text-lg" placeholder="Название бренда или заведения">
            </div>
            <div class="mb-4">
                <label class="form-label font-medium">Ваш номер телефона</label>
                <input name="phone" required type="tel" class="form-control text-lg" value="+998">
            </div>
            <div class="text-secondary">
                Мы свяжемся с вами в течение 1-2 часов
            </div>
            <div class="mt-5 gap-2 d-flex">
                <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg w-full">Отправить заявку</button>
            </div>
        </form>
    </div>
</dialog>

<dialog class="dialog mt-8" id="dialog_howto">
    <header class="dialog-header">
        <h5>Как это работает</h5>
        <button class="btn btn-icon" onclick="close_dialog(this)">
            <svg viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
                <path d="M19.0005 4.99988L5.00049 18.9999M5.00049 4.99988L19.0005 18.9999" stroke="currentColor" stroke-width="2"></path>
            </svg>
        </button>
    </header>
    <div class="dialog-body">
        <h5>Простой сценарий запуска</h5>
        <ul class="list-bullet mb-4">
            <li>1. Мы регистрируем ваш бизнес и помогаем подготовить начальную структуру меню.</li>
            <li>2. Вы получаете username для аккаунта кафе.</li>
            <li>3. Вы устанавливаете CafeMenu на планшет или подготавливаете QR-доступ для гостей.</li>
            <li>4. Меню синхронизируется из админ-панели на гостевые устройства.</li>
            <li>5. Последующие изменения можно обновлять без полной пересборки меню.</li>
        </ul>

        <h5>Когда меняются цены или изображения</h5>
        <ul class="list-bullet">
            <li>1. Откройте админ-панель с компьютера или телефона.</li>
            <li>2. Обновите блюда, цены, изображения или категории.</li>
            <li>3. Обновите устройство или приложение, чтобы загрузить актуальную версию меню.</li>
        </ul>

        <hr>
        <button onclick="close_dialog(this)" class="btn w-full btn-neutral">OK</button>
    </div>
</dialog>

<dialog class="dialog mt-8" id="dialog_cafe">
    <header class="dialog-header">
        <h5>Рестораны и кафе</h5>
        <button class="btn btn-icon" onclick="close_dialog(this)">
            <svg viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
                <path d="M19.0005 4.99988L5.00049 18.9999M5.00049 4.99988L19.0005 18.9999" stroke="currentColor" stroke-width="2"></path>
            </svg>
        </button>
    </header>
    <div class="dialog-body">
        <?php if (! empty($recentCafes)): ?>
            <ul class="list-bullet">
                <?php foreach ($recentCafes as $recentCafe): ?>
                    <li class="border-bottom md:d-flex md:justify-content-between pb-4">
                        <p class="mb-2">
                            <strong><?= esc($recentCafe['cafe_name'] ?: $recentCafe['username']) ?></strong><br>
                            Username: <?= esc($recentCafe['username']) ?>
                        </p>
                        <a class="btn btn-default" href="<?= esc(site_url($recentCafe['username'])) ?>" target="_blank" rel="nofollow noopener noreferrer">
                            Открыть пример ↗
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted my-3">Пока нет добавленных кафе.</p>
        <?php endif; ?>

        <button onclick="close_dialog(this)" class="btn w-full btn-neutral">OK</button>
    </div>
</dialog>

<script>
    function toggleMenu() {
        document.getElementById('menu_header').classList.toggle('d-none');
    }

    function open_dialog(dialog_id) {
        document.body.classList.add('overflow-hidden', 'dialog-open');
        document.getElementById(dialog_id).showModal();
    }

    function close_dialog(btn_close) {
        document.body.classList.remove('overflow-hidden', 'dialog-open');
        let this_dialog = btn_close.closest('dialog');
        this_dialog.close();
    }
</script>

</body>
</html>

<?php
$pageTitle = 'CafeMenu - Электронное меню для ресторанов, кафе и QR-меню';
$pageDescription = 'CafeMenu — это платформа электронного меню для ресторанов, кафе, баров и фудкортов. Запускайте меню на планшетах, QR-меню, мультиязычные меню и управляйте ими из одной админ-панели.';
$canonicalUrl = site_url('ru');
$englishUrl = base_url('/');
$ogImage = base_url('img/intro.png');
$contactUrl = 'https://t.me/vosidiy?text=Здравствуйте, мне нужна помощь по CafeMenu';
$tg_channel = 'https://t.me/cafemenu_uz';

$softwareSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => 'CafeMenu',
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Android, Web',
    'url' => $canonicalUrl,
    'description' => $pageDescription,
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
</head>
<body>

<header id="header" class="border-bottom py-2 bg-white">
    <div class="container md:d-flex md:flex-row md:justify-content-between md:align-items-center">
        <div class="d-flex align-items-center">
            <a href="<?= base_url(); ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
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
                <li><a href="#features" class="nav-link">Возможности</a></li>
                <li><a href="#faq" class="nav-link">Вопрос-ответ</a></li> 
                <li><a href="#" onclick="open_dialog('dialog_cafe')" class="nav-link">Рестораны </a></li>
                <li><a href="#" onclick="open_dialog('dialog_support')" class="nav-link">Контакт</a></li>
                <li><a href="<?= esc($englishUrl) ?>" class="nav-link">🇺🇸 English ↗</a></li>
            </ul>
        </nav>

        <div class="d-none md:d-block">
            <a href="<?= site_url('register') ?>" class="btn btn-default">Создать меню</a>
            <a href="<?= site_url('login') ?>" class="btn btn-orange">Вход в админ</a>
        </div>
    </div>
</header>

<div class="sm:d-none text-center p-2 border-bottom bg-neutral-100">
    <a href="<?= esc($englishUrl) ?>" class="">🇺🇸 Английская версия ↗ </a>
</div>

<section class="pt-4">
    <div class="container">
        <article class="bg-orange-100 p-5 lg:p-10" style="border-radius:24px;">
            <div class="row align-items-center">
                <main class="md:col-7 md:p-6">
                    <p class="text-orange font-semibold text-uppercase mb-3">Электронное меню</p>
                    <h1 class="font-bold text-4xl mt-3 lg:text-6xl mb-4">Цифровое меню для ресторанов и кафе: <br> QR меню и приложения для планшетов</h1>
                    <p class="text-xl mb-6">
                        CafeMenu помогает ресторанам, кафе, барам и фудкортам публиковать меню на
                        <strong class="text-orange">планшетах, смартфонах, киосках и экранах</strong>.
                        Обновляйте блюда, цены, акции и гостевой контент быстро, без ручной переделки меню на каждом устройстве.
                    </p>
                    <div class="mb-4 d-flex flex-col sm:flex-row gap-2">
                        <a href="<?= site_url('register') ?>" class="btn btn-lg btn-orange">Создать меню</a>
                        <a href="#apps" class="btn btn-lg btn-default border-orange">Скачать приложения</a>
                    </div>
                </main>
                <aside class="md:col-5">
                    <img width="578" src="img/intro.png" alt="Интерфейс CafeMenu на планшете с электронным меню ресторана, фотографиями блюд и категориями">
                </aside>
            </div>
        </article>
    </div>
</section>


<section class="pb-10 pt-18" id="features">
    <div class="container">
        <header class="text-center mx-auto mb-10" style="max-width:720px">
            <h2 class="md:text-5xl mb-4">Ключевые возможности</h2>
            <p class="text-lg text-secondary">Современная платформа электронного меню для заведений, которым нужны быстрые обновления, аккуратная подача, QR-доступ для гостей и удобная мультиязычность.</p>
        </header>

        <div class="row row-cols-1 gap-rows-3 lg:row-cols-3">
            <div class="col">
                <article class="card border-0 rounded-lg bg-red-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-1.png" height="200" alt="Планшет Android с приложением CafeMenu в ресторане" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="text-xl my-2">Меню на планшетах</h3>
                    <p>Установите приложение на Android-планшеты и дайте гостям удобный визуальный формат просмотра меню. Количество планшетов не ограничено.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border-0 rounded-lg bg-purple-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-2.png" height="200" alt="Гость сканирует QR-код и открывает меню ресторана на телефоне" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="text-xl my-2">Доступ по QR-коду</h3>
                    <p>Разместите QR-код на столе, чтобы гости открывали меню на своих смартфонах без ожидания официанта. Они смогут посмотреть блюда и цены сразу.</p>
                </article>
            </div>
            <div class="col">
                <article class="card border-0 rounded-lg bg-blue-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-3.png" height="200" alt="Экран электронного меню с категориями и фотографиями блюд" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="text-xl my-2">Готово к нестабильному интернету</h3>
                    <p>Приложение может хранить данные меню на устройстве, поэтому заведение продолжает работать даже при слабом интернете или временном отключении сети.</p>
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
                    <img class="w-full h-auto rounded-lg" height="400" src="img/feature-4.png" alt="CafeMenu admin panel on a laptop used to update restaurant menu content">
                </div>
            </aside>
            <aside class="md:col-6">
                <article class="md:ml-6">
                    <h2 class="text-3xl lg:text-5xl mb-3">Управляйте всеми устройствами из одной админ-панели</h2>
                    <hr>
                    <p class="mb-4">
                        Если у вас несколько планшетов или цифровых экранов, CafeMenu позволяет управлять ими из одной точки, а не обновлять каждое устройство вручную.
                    </p>
                    <p>Это практичная платформа электронного меню для команд, которым нужны быстрые обновления, аккуратная подача, QR-доступ для гостей и простая мультиязычная поддержка.</p>
                    <ul class="list-check mt-4 mb-4">
                        <li>Простой интерфейс для ежедневной работы с меню</li>
                        <li>Быстро добавляйте новые блюда и редактируйте категории</li>
                        <li>Обновляйте контент на подключённых устройствах из одной панели</li>
                        <li><b>Мультиязычность —</b> добавляйте переводы названий блюд 🇨🇳 🇺🇸 🇫🇷 🇪🇸 🇮🇹 🇷🇺 🇬🇧 ...</li>
                    </ul>
                </article>
            </aside>
        </div>
    </div>
</section>


<section class="py-10">
    <div class="container">
        <div class="position-relative overflow-hidden bg-orange-900 rounded-lg" style="background-image:url(img/bg-about.png)">
            <article class="p-6 lg:p-10" style="background-color:rgba(0,0,0,0.6); max-width:720px">
                <h2 class="mb-2 text-white text-2xl lg:text-5xl">Чем CafeMenu отличается от других решений?</h2>
                <ul class="d-flex flex-col gap-4 my-5 text-white list-bullet">
                    <li>
                        <h3 class="text-xl mb-2">Быстрые обновления на всех устройствах</h3>
                        <p>Когда появляется новое блюдо или меняется цена, команда обновляет меню централизованно, а не редактирует каждое устройство по отдельности.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Более сильная подача блюд</h3>
                        <p>Гости могут смотреть фотографии, читать описания, сравнивать категории и принимать решение о заказе быстрее и увереннее.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Удобство для иностранных гостей</h3>
                        <p>Мультиязычное меню помогает ресторанам понятнее обслуживать туристов и международных посетителей без лишних вопросов и путаницы.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Один продукт для всех форматов меню</h3>
                        <p>Используйте одни и те же данные меню на планшетах, телефонах гостей, киосках и экранах вместо ведения нескольких отдельных систем.</p>
                    </li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="py-10 border-top" id="apps">
    <div class="container" style="max-width:900px;">
        <header class="text-center mb-10 mx-auto">
            <h2 class="md:text-3xl mb-4">Приложения для планшетов и смартфонов</h2>
            <p class="text-lg text-secondary">Установите приложение и введите имя пользователя или код сопряжения, созданный для вашего кафе.</p>
        </header>

        <article class="card bg-secondary card-body">
            <div class="d-flex align-items-center gap-2">
                <h3 class="text-lg">APK-файл</h3>
            </div>
            <hr>
            <nav class="d-grid sm:grid-template-cols-2 gap-2">
                <a href="https://expo.dev/accounts/vosidiy/projects/cafe-menu-tablet/builds/90c214db-1f98-4c06-a304-f172cadec9c0" class="border d-block hover:border-primary rounded p-3 bg-base shadow" target="_blank" rel="noopener noreferrer">
                    <b>Cafe menu standard (expo.dev)</b> <br> Скачать
                </a>
                <a href="https://expo.dev/artifacts/eas/iSLFvsf6GKksPZdmLiyFmL.apk" class="border d-block hover:border-primary rounded p-3 bg-base shadow" target="_blank" rel="noopener noreferrer">
                    <b>Cafe menu standard (.apk)</b> <br> Скачать
                </a>
            </nav>
        </article>
        <br><br>
    </div>
</section>


<section class="py-14 bg-secondary" id="faq">
    <div class="container" style="max-width:780px;">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Часто задаваемые вопросы</h2>
            <p class="text-lg d-none text-secondary">Короткие ответы, которые помогают понять, как работает CafeMenu.</p>
        </header>
        <div class="d-grid gap-2">

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Что такое CafeMenu? </h3>
                    <p>CafeMenu — это платформа электронного меню для ресторанов, кафе, баров и фудкортов. Она помогает публиковать меню на планшетах, через QR-коды и в мультиязычном формате из одной админ-панели. Вместо ручного редактирования меню на разных устройствах команда работает с одним источником данных и быстро обновляет экраны и меню для гостей.
                    Особенно полезно это для заведений с частой сменой цен, сезонными предложениями, обновлением блюд и гостями, которым нужен доступ к меню на разных языках.</p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Кому подходит CafeMenu? </h3>
                    <p> CafeMenu создан для ресторанов, кафе, столовых, кофеен, баров и заведений самообслуживания, которым нужны быстрые обновления меню и более понятный гостевой опыт. </p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Как работает CafeMenu? </h3>
                    <p> 1. Вы создаёте аккаунт и активируете его. <br>
                        2. Добавляете категории и блюда. (одновременно у вас появляется онлайн-меню, которым можно делиться через QR-код) <br>
                        3. Устанавливаете приложение на планшет или киоск и входите по коду сопряжения или username вашего кафе. <br> Все обновления меню синхронизируются из одного места. </p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Может ли CafeMenu работать без постоянного интернета? </h3>
                    <p> Да. Приложение может хранить данные меню локально на устройстве, поэтому персонал и гости смогут пользоваться меню после первой синхронизации даже при нестабильном интернете. </p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Поддерживает ли CafeMenu несколько языков? </h3>
                    <p>Да. CafeMenu поддерживает мультиязычное меню, что помогает ресторанам понятнее обслуживать местных гостей и иностранных посетителей.</p>
                </article>

        </div>
    </div>
</section>



<footer class="border-top bg-orange-100 py-12">
    <div class="container">
        <section class="d-flex align-items-center flex-col lg:flex-row lg:justify-content-between">
            <p class="lg:mb-0 mb-3 text-center lg:text-left"><b>Cafe Menu ©</b><br>cafemenu.uz — электронное меню для ресторанов, кафе и QR-меню</p>
            <nav class="nav">
                <a href="#header" class="nav-link">Главная</a>
                <a href="#faq" class="nav-link">Вопросы и ответы</a>
                <a href="#contact" class="nav-link">Контакты</a>
            </nav>
        </section>
    </div>
</footer>



<dialog class="dialog mt-8" id="dialog_support">
    <header class="dialog-header">
        <h5>Контакт</h5>
        <button class="btn btn-icon" onclick="close_dialog(this)">
            <svg viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
                <path d="M19.0005 4.99988L5.00049 18.9999M5.00049 4.99988L19.0005 18.9999" stroke="currentColor" stroke-width="2"></path>
            </svg>
        </button>
    </header>
    <div class="dialog-body">
        <article>
            <form action="https://api.web3forms.com/submit" method="POST">
                <input type="hidden" name="access_key" value="df365b70-5ae9-4729-a504-ef3cba73313a">
                <input type="hidden" name="redirect" value="<?= base_url(); ?>thankyou">
                <input type="hidden" name="subject" value="Cafemenu Message from Website">

                <div class="mb-4">
                    <label class="form-label font-medium">Название кафе</label>
                    <input name="cafe" required type="text" class="form-control text-lg" placeholder="Название заведения">
                </div>
                <div class="mb-4">
                    <label class="form-label font-medium">Ваш номер телефона</label>
                    <input name="phone" required type="tel" class="form-control text-lg" value="+1">
                </div>
                <div class="mb-4">
                    <label class="form-label font-medium">Ваш E-mail</label>
                    <input name="email" required type="email" class="form-control text-lg">
                </div>
                <div class="text-secondary">
                    Мы свяжемся с вами в течение 1-2 часов.
                </div>
                <div class="mt-5 gap-2 d-flex">
                    <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg w-full">Отправить запрос</button>
                </div>
            </form>

            <hr>
            <p class="text-muted text-center mb-4">ИЛИ</p>
            <a href="<?= esc($contactUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-lg btn-default w-full">💬 Сообщение через Telegram</a>
        </article>
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
                        <p>
                            <strong><?= esc($recentCafe['cafe_name'] ?: $recentCafe['username']) ?></strong>
                        </p>
                        <a class="btn btn-default" href="<?= esc(site_url($recentCafe['username'])) ?>" target="_blank" rel="nofollow noopener noreferrer">
                            Открыть страницу ↗
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted my-3">Пока ни одного кафе не добавлено.</p>
        <?php endif; ?>

        <button onclick="close_dialog(this)" class="btn w-full btn-neutral">Закрыть</button>
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

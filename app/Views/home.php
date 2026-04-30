<?php
$pageTitle = 'CafeMenu - Digital Menu Software for Restaurants, Cafes, and QR Menus';
$pageDescription = 'CafeMenu is digital menu Android software (Android and APK) for restaurants, cafes, bars, and food courts. Launch tablet menus, QR menus, multilingual menus, and an admin panel from one place.';
$canonicalUrl = base_url('/');
$russianUrl = site_url('ru');
$ogImage = base_url('img/intro.png');
$contactUrl = 'https://t.me/vosidiy?text=Hello, I need help with CafeMenu';
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?></title>
    <meta name="description" content="<?= esc($pageDescription) ?>">
    <link rel="canonical" href="<?= esc($canonicalUrl) ?>">
    <link rel="alternate" hreflang="en" href="<?= esc($canonicalUrl) ?>">
    <link rel="alternate" hreflang="ru" href="<?= esc($russianUrl) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= esc($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= esc($pageTitle) ?>">
    <meta property="og:description" content="<?= esc($pageDescription) ?>">
    <meta property="og:url" content="<?= esc($canonicalUrl) ?>">
    <meta property="og:site_name" content="CafeMenu">
    <meta property="og:locale" content="en_US">
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
                <a href="<?= esc($contactUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-icon btn-default" aria-label="Contact CafeMenu on Telegram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-icon lucide-phone"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                </a>
                <a href="<?= site_url('login') ?>" class="btn btn-orange">Login</a>
                <button class="btn btn-neutral ml-1 btn-icon" onclick="toggleMenu()" aria-label="Open navigation menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                </button>
            </div>
        </div>

        <nav id="menu_header" class="d-none md:d-block" aria-label="Primary">
            <ul class="nav nav-col md:flex-row mt-4 md:mt-0">
                <li><a href="#header" class="nav-link">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#faq" class="nav-link">FAQs</a></li>
                <li><a href="#" onclick="open_dialog('dialog_cafe')" class="nav-link">Restaurants ↗</a></li>
                <li><a href="<?= esc($russianUrl) ?>" hidden class="nav-link">🇷🇺 In Russian ↗</a></li>
            </ul>
        </nav>

        <div class="d-none md:d-block">
            <a href="#contact" class="btn btn-default">Contact</a>
            <a href="<?= site_url('login') ?>" class="btn btn-orange">Admin Login</a>
        </div>
    </div>
</header>


<section class="pt-4">
    <div class="container">
        <article class="bg-orange-100 p-5 lg:p-10" style="border-radius:24px;">
            <div class="row align-items-center">
                <main class="md:col-7 md:p-6">
                    <p class="text-orange font-semibold text-uppercase mb-3">Digital menu app</p>
                    <h1 class="font-bold text-4xl mt-3 lg:text-6xl mb-4">Digital menu software for restaurants and cafes. Tablet app &amp; QR menus</h1>
                    <p class="text-xl mb-6">
                        CafeMenu helps restaurants, cafes, bars, and food courts publish menu content on
                        <strong class="text-orange">tablets, smartphones, kiosk devices, and monitors</strong>.
                        Update dishes, prices, promos, and guest-facing content quickly without rebuilding the whole menu every time.
                    </p>
                    <div class="mb-4 d-flex flex-col sm:flex-row gap-2">
                        <a href="#contact" class="btn btn-lg btn-orange">Request Demo</a>
                        <a href="#apps" class="btn btn-lg btn-default border-orange">Download apps</a>
                    </div>
                </main>
                <aside class="md:col-5">
                    <img width="578" src="img/intro.png" alt="CafeMenu tablet interface showing a digital restaurant menu with food photos and item categories">
                </aside>
            </div>
        </article>
    </div>
</section>


<section class="pb-10 pt-18" id="features">
    <div class="container">
        <header class="text-center mx-auto mb-10" style="max-width:720px">
            <h2 class="md:text-5xl mb-4">Main Features</h2>
            <p class="text-lg text-secondary"> Modern e-menu platform for teams that need faster menu updates, cleaner presentation, QR access for guests, and easy multilingual support. </p>
        </header>

        <div class="row row-cols-1 gap-rows-3 lg:row-cols-3">
            <div class="col">
                <article class="card border-0 rounded-lg bg-red-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-1.png" height="200" alt="Android tablet running CafeMenu inside a restaurant" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="text-xl my-2">Tablet menu app</h3>
                    <p>Install the menu app on Android tablets and give guests a visual, touch-friendly browsing experience. Unlimited number of tablets allowed</p>
                </article>
            </div>
            <div class="col">
                <article class="card border-0 rounded-lg bg-purple-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-2.png" height="200" alt="Guest scanning a QR code to open a restaurant menu on a phone" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="text-xl my-2">QR menu access</h3>
                    <p>Place a QR code on the table so customers can open the menu on their own smartphones without waiting. They can select food and see prices</p>
                </article>
            </div>
            <div class="col">
                <article class="card border-0 rounded-lg bg-blue-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-3.png" height="200" alt="Digital menu screen showing food categories and dish photos" class="w-100% h-40 rounded">
                    </div>
                    <h3 class="text-xl my-2">Offline-ready</h3>
                    <p>The app can keep menu data on the device, which helps venues keep working even with unstable internet or no internet.</p>
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
                    <h2 class="text-3xl lg:text-5xl mb-3">Manage all devices through one admin panel</h2>
                    <hr>
                    <p class="mb-4">
                        If your business uses multiple tablets or digital screens, CafeMenu lets you manage them from one place instead of updating each device separately.
                    </p>
                    <p> This is a practical e-menu platform for teams that need faster menu updates, cleaner presentation, QR access for guests, and easy multilingual support.</p>
                    <ul class="list-check mt-4 mb-4">
                        <li>Simple interface for menu operations</li>
                        <li>Add new or edit dishes and categories quickly</li>
                        <li>Refresh menu content on connected devices</li>
                        <li><b>Multi-language -</b> add translation of food names 🇨🇳 🇺🇸 🇫🇷 🇪🇸 🇮🇹 🇷🇺 🇬🇧 ...</li>
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
                <h2 class="mb-2 text-white text-2xl lg:text-5xl">What makes CafeMenu different?</h2>
                <ul class="d-flex flex-col gap-4 my-5 text-white list-bullet">
                    <li>
                        <h3 class="text-xl mb-2">Fast updates across devices</h3>
                        <p>When a new dish is added or a price changes, your team can update the menu centrally instead of editing each device manually.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">More useful dish presentation</h3>
                        <p>Guests can browse dish images, read descriptions, compare categories, and make clearer choices.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">Support for foreign-language guests</h3>
                        <p>Multilingual menu content helps restaurants serve tourists and international visitors with less confusion.</p>
                    </li>
                    <li>
                        <h3 class="text-xl mb-2">One product for several menu surfaces</h3>
                        <p>Use the same menu data across tablets, customer phones, kiosks, and screens instead of managing separate systems.</p>
                    </li>
                </ul>
            </article>
        </div>
    </div>
</section>



<section class="pt-10 pb-16" id="contact">
    <div class="container">
        <div class="row">
            <div class="md:col-6 mx-auto">
                <article class="card shadow-lg bg-neutral-100 border-orange border-width-3 md:p-8 p-4 rounded-lg">
                    <h2 class="mb-3 text-2xl lg:text-3xl">Request a CafeMenu demo</h2>
                    <p class="mb-5 text-secondary">Tell us about your cafe, restaurant, or bar, and we will get back to you with a demo and implementation guidance.</p>
                    <form action="https://api.web3forms.com/submit" method="POST">
                        <input type="hidden" name="access_key" value="df365b70-5ae9-4729-a504-ef3cba73313a">
                        <input type="hidden" name="redirect" value="<?= base_url(); ?>thankyou">
                        <input type="hidden" name="subject" value="Cafemenu Message from Website">

                        <div class="mb-4">
                            <label class="form-label font-medium">Cafe name</label>
                            <input name="cafe" required type="text" class="form-control text-lg" placeholder="Brand or venue name">
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-medium">Your phone number</label>
                            <input name="phone" required type="tel" class="form-control text-lg" value="+1">
                        </div>
                        <div class="text-secondary">
                            We will get back to you within 1-2 hours
                        </div>
                        <div class="mt-5 gap-2 d-flex">
                            <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg w-full">Send Request</button>
                        </div>
                    </form>

                    <hr>
                    <p class="text-muted text-center mb-4">OR</p>
                    <a href="<?= esc($contactUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-lg btn-default w-full">Message on Telegram</a>
                </article>
            </div>
        </div>
    </div>
</section>


<section class="py-10 border-top" id="apps">
    <div class="container" style="max-width:900px;">
        <header class="text-center mb-10 mx-auto">
            <h2 class="md:text-3xl mb-4">Tablet / smartphone apps</h2>
            <p class="text-lg text-secondary">Install the app and enter the username or pairing code created for your cafe. </p>
        </header>

        <article class="card bg-secondary card-body">
            <div class="d-flex align-items-center gap-2">
                <h3 class="text-lg">APK file</h3>
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


<section class="py-14 bg-secondary" id="faq">
    <div class="container" style="max-width:780px;">
        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Frequently asked questions</h2>
            <p class="text-lg d-none text-secondary">These short answers explain how CafeMenu works for search users and AI chat retrieval.</p>
        </header>
        <div class="d-grid gap-2">

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> What is CafeMenu? </h3>
                    <p>CafeMenu is digital menu software for restaurants, cafes, bars, and food courts. It helps businesses publish tablet menus, QR menus, and multilingual food menus from one admin panel.  Instead of editing menus manually across devices, your team manages one source of truth and pushes updates to customer-facing screens and menu apps.
                    It is especially useful for businesses with frequent price changes, rotating dishes, seasonal promotions, or guests who need menu access in multiple languages.</p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Who should use CafeMenu? </h3>
                    <p> CafeMenu is built for restaurants, cafes, canteens, coffee shops, bars, and self-service food businesses that want faster menu updates and a clearer guest experience. </p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> How does CafeMenu work? </h3>
                    <p> 1. You create an account and activate it. <br>
                        2. Create categories and menu items. (you will also have online menu that can be shared with QR code) <br>
                        3. install the app on a tablet or kiosk and use pairing code or enter your cafe\'s username <br> Menu updates sync from one place. </p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Can CafeMenu work without constant internet access? </h3>
                    <p> Yes. The app can store menu data on the device locally so staff and guests can keep using the menu after the first sync even when internet access is unstable. </p>
                </article>

                <article class="border-bottom border-color-strong pb-4 pt-2">
                    <h3 class="text-lg mb-1"> Does CafeMenu support multiple languages? </h3>
                    <p>Yes. CafeMenu supports multilingual menu content, which helps restaurants serve local guests and foreign visitors more clearly.</p>
                </article>

        </div>
    </div>
</section>




<footer class="border-top bg-orange-100 py-12">
    <div class="container">
        <section class="d-flex align-items-center flex-col lg:flex-row lg:justify-content-between">
            <p class="lg:mb-0 mb-3 text-center lg:text-left"><b>Cafe Menu ©</b><br>cafemenu.uz - digital menu software for restaurants, cafes, and QR menus</p>
            <nav class="nav">
                <a href="#header" class="nav-link">Home</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>
        </section>
    </div>
</footer>

<dialog class="dialog mt-8" id="form_request">
    <header class="dialog-header">
        <h5>Request Demo</h5>
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
            <input type="hidden" name="subject" value="Cafemenu Message from Website">

            <div class="mb-4">
                <label class="form-label font-medium">Cafe name</label>
                <input name="cafe" required type="text" class="form-control text-lg" placeholder="Brand or venue name">
            </div>
            <div class="mb-4">
                <label class="form-label font-medium">Your phone number</label>
                <input name="phone" required type="tel" class="form-control text-lg" value="+998">
            </div>
            <div class="text-secondary">
                We will get back to you within 1-2 hours
            </div>
            <div class="mt-5 gap-2 d-flex">
                <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg w-full">Send Request</button>
            </div>
        </form>
    </div>
</dialog>


<dialog class="dialog mt-8" id="dialog_cafe">
    <header class="dialog-header">
        <h5>Restaurants and Cafes</h5>
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
                            Open page ↗
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted my-3">No cafes have been added yet.</p>
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

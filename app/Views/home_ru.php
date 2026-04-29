<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CafeMenu - Elektron Menu, меню на планшете для ресторана и кафе</title>
    <meta name="description" content="Онлайн меню для ресторана, кафе и баров, меню на планшете, qr меню для ресторан. Cafe Menu">
    <link href="final.min.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">

</head>
<body>

<header id="header" class="border-bottom py-2 bg-white">
    <div class="container md:d-flex md:flex-row md:justify-content-between md:align-items-center">
        <div class="d-flex align-items-center">
            <!-- brand -->
            <a href="<?= base_url(); ?>ru" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
                <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36" fill="#b85000"><path d="M226.67-500v-234h234v234h-234Zm0 274v-234h234v234h-234ZM500-500v-234h234v234H500Zm0 274v-234h234v234H500ZM186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586.66q0 27-19.83 46.84Q800.33-120 773.33-120H186.67Zm0-66.67h586.66v-586.66H186.67v586.66Z"/></svg>
                <div style="font-size:24px;" class="text-orange-800 font-semibold ml-1">Cafe<span class="text-secondary">Menu</span> </div>
            </a>
            <!-- brand .//end -->
            <!-- mobile-actions -->
            <div class="md:d-none ml-auto">
                <a href="https://t.me/vosidiy?text=Salom alaykum, CafeMenu masalasida yordam kerak" target="_blank" class="btn btn-icon btn-default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-icon lucide-phone"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                </a>
                <a href="<?= site_url('admin/login') ?>" class="btn btn-orange"> Войти </a>
                <button class="btn btn-neutral ml-1 btn-icon" onclick="toggleMenu()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                </button>
            </div>
            <!-- mobile-actions -->
        </div>

        <nav id="menu_header" class="d-none md:d-block">
            <ul class="nav nav-col md:flex-row mt-4 md:mt-0">
                <li><a href="#header" class="nav-link">Главная</a></li>
                <li><a href="#features" class="nav-link">Возможности</a></li>
                <li><a href="#" onclick="open_dialog('dialog_howto')" class="nav-link">Инструкция ↗</a></li>
                <li><a href="#" onclick="open_dialog('dialog_cafe')" class="nav-link">Рестораны ↗</a></li>
                <li><a href="/" class="nav-link"> 🇺🇿 O'zbekcha</a></li>
            </ul>
        </nav>

        <div class="d-none md:d-block">
            <a onclick="open_dialog('form_request')" class="btn btn-default"> Связаться </a>
            <a href="<?= site_url('admin/login') ?>" class="btn btn-orange"> Вход в админ </a>
        </div>
    </div>  <!-- container .// -->
</header>

<section class="pt-4">
    <div class="container">
        <article class="bg-orange-100 p-5 lg:p-10" style="border-radius:24px;">
            <div class="row">
                <main class="md:col-7 md:p-6">
                    <h1 class="font-bold text-4xl mt-7 lg:text-6xl mb-4">Программа электронного меню для кафе и ресторанов</h1>
                    <p class="text-xl mb-8">
                        Показывайте нужную информацию на экранах <strong class="text-orange">планшетов, смартфонов, киосков и мониторов</strong>. Меню блюд, объявления, акции и другое можно <strong  class="text-orange">легко управлять и автоматически обновлять</strong>
                    </p>
                    <div class="mb-4 d-flex flex-col sm:flex-row gap-2">
                        <button onclick="open_dialog('form_request')" class="btn btn-lg btn-orange">Посмотреть демо</button>
                        <a href="<?= site_url('admin/login') ?>" class="btn  btn-lg btn-default border-orange" rel="noopener noreferrer">Войти как админ</a>
                    </div>
                </main>
                <aside class="md:col-5">
                    <img width="578" src="img/intro.png" alt="eMenu или электронное меню, Tablet device food menu UI for restaurant">
                </aside>
            </div>
        </article>
    </div> <!-- container.// -->
</section>


<section class="pb-10 pt-18" id="features">
	<div class="container">

        <header class="text-center mb-10">
            <h2 class="md:text-5xl mb-4">Возможности</h2>
            <p class="text-lg text-secondary"> Создайте ещё больше удобства для ваших клиентов с помощью современных технологий </p>
        </header>
    
		<div class="row row-cols-1 gap-rows-3 lg:row-cols-3">
			<div class="col">
				<article class="card border-0 rounded-lg bg-red-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-1.png" height="200" alt="Tablet device" class="w-100% h-40 rounded">
                    </div>
                    <h4 class="my-2"> Приложение для планшета </h4>
                    <p> Установите мобильное приложение на любое Android-устройство</p>
                </article>
			</div>
			<div class="col">
                <article class="card border-0 rounded-lg bg-purple-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-2.png" height="200" alt="QR scanning in cafe" class="w-100% h-40 rounded">
                    </div>
                    <h4 class="my-2"> Через QR-код </h4>
                    <p> Разместите QR-код на столе. Клиенты смогут самостоятельно выбрать нужные блюда </p>
                </article>
			</div>
			<div class="col">
                <article class="card border-0 rounded-lg bg-blue-100 p-5">
                    <div class="bg-primary-200 mb-3 rounded" style="min-height: 160px;">
                        <img src="img/feature-3.png" height="200" alt="Tablet menu in cafe" class="w-100% h-40 rounded">
                    </div>
                    <h4 class="my-2"> Офлайн и онлайн </h4>
                    <p> Интернет не обязателен. Мобильное приложение хранит всю информацию локально </p>
                </article>
			</div>
		</div>
	</div>  <!-- container.// -->
</section>

<section class="py-10">
	<div class="container">
        <div class="row justify-content-between align-items-center">
            <aside class="md:col-6">
                    <div class="rounded-lg bg-primary-200  mb-4">
                            <img class="w-full h-auto rounded-lg" height="400" src="img/feature-4.png" alt="Cafe admin panel on laptop">
                    </div>
            </aside>
            <aside class="md:col-6">
                <article class="md:ml-6">
                        <h2 class="text-3xl lg:text-5xl mb-3">
                            Управляйте всеми устройствами через единую админ-панель
                        </h2>
                        <hr>
                        <p>
                          Если у вас много планшетов, вы можете управлять ими всеми из одного места: добавлять новые блюда, удалять, редактировать и выполнять другие действия
                        </p>
                        <ul class="list-check mt-4 mb-4">
                            <li>
                               Простой и удобный интерфейс
                            </li>
                            <li>
                                Добавление новых позиций меню
                            </li>
                            <li>
                                 Редактирование и удаление данных
                            </li>
                            <li>
                                 Обновление цен
                            </li>
                        </ul>
                </article>
            </aside>     
        </div>
</div>  <!-- container.// -->
</section>

<section class="py-10">
	<div class="container">
		<div class="position-relative overflow-hidden bg-orange-900 rounded-lg" style="background-image:url(img/bg-about.png)">
                <article class="p-6 lg:p-10" style="background-color:rgba(0,0,0,0.6); max-width:600px">
                        <h2 class="mb-2 text-white text-2xl lg:text-5xl">
                            Чем удобно <br> электронное меню?
                        </h2>
                        <ul class="d-flex flex-col gap-4 my-5 text-white list-bullet">
                            <li>
                                <h5 class="text-xl mb-2">Мгновенное и простое обновление </h5>
                                <p>Если добавляется новое блюдо <br> или меняется цена, это сразу отражается на всех устройствах</p>
                            </li>
                            <li>
                                <h5 class="text-xl mb-2">Больше информации о блюде </h5>
                                <p>На планшете можно увеличить фото блюда, выбрать его и увидеть общую сумму</p>
                            </li>
                            <li>
                                <h5 class="text-xl mb-2">И на смартфоне клиента... </h5>
                                <p>Клиент может просматривать и выбирать блюда на своём телефоне, отсканировав QR-код</p>
                            </li>
                            
                            <li>
                                <h5 class="text-xl mb-2">Показ и на иностранных языках </h5>
                                <p>Если клиент иностранец, электронное меню может показывать информацию о блюдах и на английском языке</p>
                            </li>
                        </ul>
                </article>
			</div>
		</div>
	</div> <!-- container.// -->
</section>


<section class="py-10">
	<div class="container">
			<div class="row">
                <div class="md:col-6 mx-auto">
                    <article class="card shadow-lg bg-neutral-100 border-orange border-width-3 md:p-8 p-4 rounded-lg">
                        <h5 class="mb-5 text-2xl lg:text-3xl"> Внедрим?
                        </h5>
                        <form action="https://api.web3forms.com/submit" method="POST">
                            <input type="hidden" name="access_key" value="df365b70-5ae9-4729-a504-ef3cba73313a">
                            <input type="hidden" name="redirect" value="<?= base_url(); ?>thankyou">
                            <input type="hidden" name="subject" value="Cafemenu Maktub Saytdan">

                            <div class="mb-4">
                                <label class="form-label font-medium">Название кафе</label>
                                <input name="cafe" required type="text" class="form-control text-lg" placeholder="Название бренда или заведения">
                            </div>
                            <div class="mb-4">
                                <label class="form-label font-medium">Ваш номер телефона</label>
                                <input name="phone" required type="tel" class="form-control text-lg" placeholder="" value="+998">
                            </div>
                            <div class="text-secondary">
                                Мы свяжемся с вами в течение 1-2 часов
                            </div>
                            <div class="mt-5 gap-2 d-flex">
                                <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg  w-full"> Отправить заявку </button>
                            </div>
                        </form>

                        <hr>
                        <p class="text-muted text-center mb-4">ИЛИ</p>
                        <a href="https://t.me/vosidiy?text=Salom alaykum, CafeMenu masalasida yordam kerak" target="_blank" class="btn btn-lg btn-default w-full"> Написать в Telegram </a>
                    </article>
                </div>
			</div>
	</div> <!-- container.// -->
</section>

<section class="py-10">
	<div class="container"  style="max-width:900px;">
        
        <header class="text-center mb-10 mx-auto">
            <h2 class="md:text-3xl mb-4">Приложения для планшета / смартфона</h2>
            <p class="text-lg text-secondary"> Установите приложение и введите <u>username</u>, созданный для кафе. Программа автоматически сохранит созданное вами меню. Для первой загрузки или обновления меню требуется подключение к интернету. <br> Сначала у вас должен быть аккаунт для создания меню </p>
        </header>

        <article hidden class="card bg-secondary card-body mb-4">
            <div class="d-flex align-items-center gap-2">
                <svg height="24" width="24" viewBox="0 0 135 148" xmlns="http://www.w3.org/2000/svg"><path d="m63.249 70.894-62.67 65.216.009.04c1.923 7.08 8.518 12.295 16.349 12.295 3.128 0 6.067-.83 8.588-2.285l.2-.118 70.533-39.915z" fill="#ea4335"/><path d="m126.65 59.788-.06-.039-30.454-17.312-34.31 29.937 34.431 33.753 30.294-17.138c5.31-2.813 8.916-8.301 8.916-14.63 0-6.29-3.556-11.748-8.817-14.57z" fill="#fbbc04"/><path d="m.574 12.331a16.036 16.036 0 0 0 -.574 4.271v115.241c.061 1.654.197 2.912.578 4.267l64.826-63.546z" fill="#4285f4"/><path d="m63.71 74.223 32.426-31.786-70.452-40.064a17.222 17.222 0 0 0 -8.747-2.373c-7.83 0-14.437 5.225-16.358 12.315l-.005.017z" fill="#34a853"/></svg>
                <h5 class="">Через Google Play</h5>
            </div>
            <hr>
            <nav class="d-grid sm:grid-template-cols-2  gap-2">
                <a href="#" class="border  d-block hover:border-primary rounded p-3 bg-base shadow" target="_blank">
                    <b>Cafe menu standard</b> <br> Download
                </a>
                <a href="#" class="border  d-block hover:border-primary  rounded p-3 bg-base shadow" target="_blank">
                    <b>Cafe menu kiosk mode</b> <br> Download
                </a>
            </nav>
        </article>

        <article class="card bg-secondary card-body">
            <div class="d-flex align-items-center gap-2">
                <h5 class="">APK-файл</h5>
            </div>
            <hr>
            <nav class="d-grid sm:grid-template-cols-2 gap-2">
                <a href="https://expo.dev/accounts/vosidiy/projects/cafe-menu-tablet/builds/90c214db-1f98-4c06-a304-f172cadec9c0" class="border  d-block hover:border-primary rounded p-3 bg-base shadow" target="_blank">
                    <b>Cafe menu standard (expo.dev) </b> <br> Download
                </a>
                <a href="https://expo.dev/artifacts/eas/iSLFvsf6GKksPZdmLiyFmL.apk" class="border d-block hover:border-primary  rounded p-3 bg-base shadow" target="_blank">
                    <b>Cafe menu standard (.apk)</b> <br> Download
                </a>
            </nav>
        </article>
        <br><br>
	</div> <!-- container.// -->
</section>

<footer class="border-top bg-orange-100 py-12">
    <div class="container">
        <section class="d-flex align-items-center flex-col lg:flex-row lg:justify-content-between">
                <p class="lg:mb-0 mb-3 text-center lg:text-left"> <b>Cafe Menu © </b> <br> cafemenu.uz - удобная программа электронного меню для кафе и ресторанов  </p>  
                <nav class="nav">
                        <a href="<?= base_url(); ?>" class="nav-link">Главная</a>
                        <a href="#" class="nav-link"> Android-приложение </a>
                </nav>
            </section>
    </div><!-- //container -->
</footer>




<dialog class="dialog mt-8" id="form_request">
    <header class="dialog-header">
        <h5>Посмотреть демо</h5>
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
        <input type="hidden" name="subject" value="Cafemenu Maktub Saytdan">

        <div class="mb-4">
            <label class="form-label font-medium">Название кафе</label>
            <input name="cafe" required type="text" class="form-control text-lg" placeholder="Название бренда или заведения">
        </div>
        <div class="mb-4">
            <label class="form-label font-medium">Ваш номер телефона</label>
            <input name="phone" required type="tel" class="form-control text-lg" placeholder="" value="+998">
        </div>
        <div class="text-secondary">
            Мы свяжемся с вами в течение 1-2 часов
        </div>
        <div class="mt-5 gap-2 d-flex">
            <button type="submit" class="btn flex-1 btn-primary bg-dark btn-lg  w-full"> Отправить заявку </button>
        </div>
    </form>
    </div>
</dialog>



<dialog class="dialog mt-8" id="dialog_howto">
    <header class="dialog-header">
        <h5>Инструкция</h5>
        <button class="btn btn-icon" onclick="close_dialog(this)">
            <svg viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
                <path d="M19.0005 4.99988L5.00049 18.9999M5.00049 4.99988L19.0005 18.9999" stroke="currentColor" stroke-width="2"></path>
            </svg> 
        </button> 
    </header>
    <div class="dialog-body">

        <h5>Как это работает?</h5>
        <ul class="list-bullet mb-4">
            <li>1. Мы зарегистрируем вас и создадим начальное меню (список блюд). </li>
            <li>2. Вам будет выдан `username` (логин)</li>
            <li>3. Вы установите наше приложение Cafemenu для планшета (Android)</li>
            <li>4. В приложение на планшете вводится `username`.</li>
            <li>5. Меню автоматически загрузится на планшет</li>
        </ul>   

        <h5>Что делать, если изменились цены или изображения в меню?</h5>
        <ul  class="list-bullet">
            <li>1. Откройте браузер на компьютере или телефоне. </li>
            <li>2. Зайдите на этот сайт: <?= base_url(); ?> и откройте страницу Admin. Вход выполняется по логину и паролю </li>
            <li>3. Измените или добавьте нужную информацию</li>
            <li>4. На планшете нажмите кнопку `Обновить` в верхней части экрана </li>
        </ul>

    <hr>
    <button onclick="close_dialog(this)" class="btn w-full btn-neutral"> OK </button>
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
                    <strong><?= esc($recentCafe['cafe_name'] ?: $recentCafe['username']) ?></strong> <br> 
                    Username: <?= esc($recentCafe['username']) ?>
                </p>
                <a class="btn btn-default" href="<?= esc(site_url($recentCafe['username'])) ?>"  target="_blank" rel="noopener noreferrer">
                    Menu: <?= esc(site_url($recentCafe['username'])) ?> ↗
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p class="text-muted my-3">Кафе пока не добавлены.</p>
    <?php endif; ?>

    <button onclick="close_dialog(this)" class="btn w-full btn-neutral"> OK </button>
    </div>
</dialog>



<script>
		// Mobile menu  toggle
        function toggleMenu() {
            document.getElementById('menu_header').classList.toggle('d-none');
        }

        function open_dialog(dialog_id){
            document.body.classList.add('overflow-hidden', 'dialog-open');
            document.getElementById(dialog_id).showModal();
        }

        function close_dialog(btn_close){
            document.body.classList.remove('overflow-hidden', 'dialog-open');
            let this_dialog = btn_close.closest('dialog')
            this_dialog.close();    
        }
	</script>

</body>
</html>

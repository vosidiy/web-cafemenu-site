<?php
$landingLinks = array_replace([
    'contact_url' => '#',
], $landingLinks ?? []);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CafeMenu.site - Thanks</title>
    <meta name="robots" content="noindex,nofollow">
    <link href="final.min.css" rel="stylesheet">
</head>
<body>

<header id="header" class="border-bottom py-2 bg-white">
<div class="container md:d-flex md:flex-row md:justify-content-between md:align-items-center">
    <div class="d-flex align-items-center">
        <!-- brand -->
        <a href="<?= base_url(); ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
            <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36" fill="#b85000"><path d="M226.67-500v-234h234v234h-234Zm0 274v-234h234v234h-234ZM500-500v-234h234v234H500Zm0 274v-234h234v234H500ZM186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586.66q0 27-19.83 46.84Q800.33-120 773.33-120H186.67Zm0-66.67h586.66v-586.66H186.67v586.66Z"/></svg>
            <div style="font-size:24px;" class="text-orange-800 font-semibold ml-1">Cafe<span class="text-secondary">Menu</span> </div>
        </a>
        <!-- brand .//end -->
    </div>

</div>  <!-- container .// -->
</header>


<div class="container">
<article class="d-flex flex-col align-items-center justify-content-center text-center mt-10"> 

<svg width="100" height="100" class="text-green-500 dark:text-green-300" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26.6666 50L46.6666 66.6667L73.3333 33.3333M50 96.6667C43.8716 96.6667 37.8033 95.4596 32.1414 93.1144C26.4796 90.7692 21.3351 87.3317 17.0017 82.9983C12.6683 78.6649 9.23082 73.5204 6.8856 67.8586C4.54038 62.1967 3.33331 56.1283 3.33331 50C3.33331 43.8716 4.54038 37.8033 6.8856 32.1414C9.23082 26.4796 12.6683 21.3351 17.0017 17.0017C21.3351 12.6683 26.4796 9.23084 32.1414 6.88562C37.8033 4.5404 43.8716 3.33333 50 3.33333C62.3767 3.33333 74.2466 8.24998 82.9983 17.0017C91.75 25.7534 96.6666 37.6232 96.6666 50C96.6666 62.3768 91.75 74.2466 82.9983 82.9983C74.2466 91.75 62.3767 96.6667 50 96.6667Z" stroke="currentColor" stroke-width="3"></path>
</svg>

<div>
  <h3 class="text-2xl text-green-500 pt-5 mb-3">Thanks, We will contact you</h3>
  <p>Within 1-2 hours</p>
  <p>If you want you can message us via Telegram</p>
  <p class="mt-2">🇷🇺 Спасибо, мы получили вашу информацию. <br> Мы свяжемся с вами в течение дня. <br> Если вы хотите связаться с нами, пожалуйста, сделайте это сейчас.</p>
  <hr>
  <a href="<?= esc($landingLinks['contact_url']) ?>" target="_blank" class="btn  btn-default">
   💬 Chat via Messenger
  </a>
<br><br>
  <a href="<?= base_url(); ?>" class="btn  btn-neutral">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left-icon lucide-arrow-left"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg> 
    Go back / Назад
  </a>

</div>

</article>

</div> <!-- container.// -->

</body>
</html>

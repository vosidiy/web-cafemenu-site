<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($cafe['cafe_name'] ?: $username) ?></title>
    <meta name="robots" content="noindex,follow">
    <meta name="theme-color" content="#b45309">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="<?= esc(site_url($username . '/manifest.webmanifest')) ?>">
    <link rel="apple-touch-icon" href="<?= esc(base_url('icon-192.png')) ?>">
    <link rel="stylesheet" href="<?= esc(base_url('style.css')) ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css">

    <link rel="icon" href="/icon-192.png" type="image/png" />

</head>
<body>


<div id="menuApp" v-cloak>
    <header class="app-header">
        <h1>{{ cafeName }}</h1>
        <div class="status-row">
            <select
                v-if="showLanguageSwitcher"
                v-model="selectedLanguage"
                class="btn btn--ghost btn--sm"
                aria-label="Menu language"
            >
                <option
                    v-for="language in availableLanguages"
                    :key="language.code"
                    :value="language.code"
                >
                    {{ language.flag }} {{ language.native_label }}
                </option>
            </select>
            <button
                v-if="!isAppInstalled"
                type="button"
                class="btn btn--ghost btn--sm"
                :disabled="!canInstall"
                @click="promptInstall"
            >
                Установить
            </button>
            <span class="badge">Обновлено: {{ updatedAtLabel }}</span>
        </div>
    </header>

    <nav id="categories" class="categories" aria-label="Категории меню" :lang="selectedLanguage" :dir="currentDirection">
        <button
            type="button"
            class="cat-btn"
            :aria-pressed="selectedCategoryId === 'all'"
            @click="selectCategory('all')"
        >
            Все блюда
        </button>
        <button
            v-for="category in categories"
            :key="category.id"
            type="button"
            class="cat-btn"
            :aria-pressed="selectedCategoryId === category.id"
            @click="selectCategory(category.id)"
        >
            {{ categoryName(category) }}
        </button>
    </nav>

    <main class="menu-main" :lang="selectedLanguage" :dir="currentDirection">
        <div v-if="isLoading" class="error-state">Загрузка меню...</div>
        <div v-else-if="errorMessage" class="error-state">{{ errorMessage }}</div>
        <div v-else-if="items.length === 0" class="error-state">Пункты меню отсутствуют.</div>
        <div v-else-if="isAllFoodSelected" class="menu-sections">
            <section  v-for="group in groupedItems" :key="group.id" class="menu-section">
                <div class="menu-section__head">
                    <h2 class="menu-section__title">{{ group.display_name }}</h2>
                </div>
                <div class="item-grid">
                    <article v-for="item in group.items" :key="item.id" class="item-card">
                        <div class="item-card__img-wrap">
                            <a
                                class="item-card__img-link"
                                :href="itemImage(item)"
                                data-fancybox
                            >
                                <img class="item-card__img" :src="itemImage(item)" :alt="itemName(item)" loading="lazy" decoding="async">
                            </a>
                        </div>
                        <div class="item-card__body p-3">
                            <div class="mb-2">
                                
                                <button v-if="itemQuantity(item.id) === 0"
                                    type="button" class="btn w-full"
                                    :disabled="!item.is_available" @click="incrementItem(item.id)">
                                    {{ item.is_available ? 'Выбирать' : 'Недоступно' }}
                                </button>

                                <div v-else class="item-added-control">
                                    <button type="button" class="btn btn--qty" aria-label="Уменьшить"
                                        @click="decrementItem(item.id)">
                                        −
                                    </button>
                                    <span class="qty-label">{{ itemQuantity(item.id) }}</span>
                                    <button type="button" class="btn btn--qty" aria-label="Увеличить"
                                        @click="incrementItem(item.id)">
                                        +
                                    </button>
                                </div>
                            </div>

                            <h3 class="item-card__title">{{ itemName(item) }}</h3>
                            <p v-if="itemDescription(item)" class="item-card__desc mb-2">{{ itemDescription(item) }}</p>
                            <p class="item-card__price">
                                <span>{{ formatPrice(item.price) }}</span>
                                <span>{{ currencyLabel }}</span>
                            </p>
                        </div>
                    </article>
                </div>
            </section>
        </div>
        <div v-else-if="filteredItems.length === 0" class="error-state">В этой категории блюда отсутствуют.</div>
        <div v-else class="item-grid">
            <article
                v-for="item in filteredItems"
                :key="item.id"
                class="item-card"
            >
                <div class="item-card__img-wrap">
                    <a class="item-card__img-link" :href="itemImage(item)"  data-fancybox>
                        <img class="item-card__img" :src="itemImage(item)" :alt="itemName(item)" loading="lazy" decoding="async">
                    </a>
                </div>

                <div class="item-card__body p-3">
                    <div class="mb-2">
                        <button
                            v-if="itemQuantity(item.id) === 0"
                            type="button"
                            class="btn w-full"
                            :disabled="!item.is_available"
                            @click="incrementItem(item.id)"
                        >
                            {{ item.is_available ? 'Выбирать' : 'Недоступно' }}
                        </button>

                        <div v-else class="item-added-control">
                            <button
                                type="button"
                                class="btn btn--qty"
                                aria-label="Уменьшить"
                                @click="decrementItem(item.id)"
                            >
                                −
                            </button>
                            <span class="qty-label">{{ itemQuantity(item.id) }}</span>
                            <button
                                type="button"
                                class="btn btn--qty"
                                aria-label="Увеличить"
                                @click="incrementItem(item.id)"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <h3 class="item-card__title">{{ itemName(item) }}</h3>
                    <p v-if="itemDescription(item)" class="item-card__desc mb-2">{{ itemDescription(item) }}</p>
                    <p class="item-card__price">
                        <span>{{ formatPrice(item.price) }}</span>
                        <span>{{ currencyLabel }}</span>
                    </p>
                    
                </div>
            </article>
        </div>
    </main>

    <div class="cart-bar" :class="{ 'is-active': cartHasItems }">
        <div class="cart-bar__copy">
            <strong v-if="cafeSlogan" class="cart-bar__title">{{ cafeSlogan }}</strong>
            <span class="cart-bar__caption">{{ cartBarTitle }}. {{ cartBarCaption }}</span>
        </div>
        <button
            type="button"
            class="btn cart-bar__button"
            id="open-cart"
            :disabled="!cartHasItems"
            @click="openCart"
        >
            Вибрано ({{ cartItemCount }})
        </button>
    </div>

    <div
        id="cart-panel"
        class="cart-panel"
        :class="{ 'is-open': isCartOpen }"
        :aria-hidden="String(!isCartOpen)"
    >
        <div
            class="cart-panel__backdrop"
            id="cart-backdrop"
            @click="closeCart"
        ></div>

        <aside
            id="cart-dialog"
            ref="cartDialog"
            class="cart-panel__sheet"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cart-title"
            :tabindex="isCartOpen ? 0 : -1"
        >
            <header class="cart-panel__head">
                <div>
                    <h2 id="cart-title">Выбранные блюда</h2>
                    <p class="cart-panel__hint">Покажите официанту</p>
                </div>
                <button
                    type="button"
                    class="btn btn--ghost"
                    id="close-cart"
                    @click="closeCart"
                >
                    Закрыть
                </button>
            </header>

            <div id="cart_items" class="cart-lines">
                <div v-if="!cartHasItems" class="empty-cart">
                    Пока ничего не выбрано.
                </div>

                <template v-else>
                    <article
                        v-for="cartItem in cartItems"
                        :key="cartItem.id"
                        class="cart-line"
                    >
                        <div>
                            <h5 class="cart-line__title">{{ itemName(cartItem) }}</h5>
                            <p class="cart-line__meta">
                                {{ formatPrice(cartItem.price) }} {{ currencyLabel }} × {{ cartItem.quantity }}
                            </p>
                        </div>

                        <div class="cart-line__controls">
                            <button
                                type="button"
                                class="btn btn--qty"
                                aria-label="Уменьшить"
                                @click="decrementItem(cartItem.id)"
                            >
                                −
                            </button>
                            <span class="qty-label">{{ cartItem.quantity }}</span>
                            <button
                                type="button"
                                class="btn btn--qty"
                                aria-label="Увеличить"
                                @click="incrementItem(cartItem.id)"
                            >
                                +
                            </button>
                            <button
                                type="button"
                                class="btn btn--ghost btn--remove"
                                aria-label="Убрать из корзины"
                                @click="removeItem(cartItem.id)"
                            >
                                Убрать
                            </button>
                        </div>
                    </article>
                </template>
            </div>

            <footer class="cart-panel__foot">
                <div class="cart-total">
                    <span>Итого</span>
                    <span id="cart-total">{{ formatPrice(cartTotal) }} {{ currencyLabel }}</span>
                </div>
            </footer>
        </aside>
    </div>
</div>



<script>
    window.MenuAppConfig = {
        jsonUrl: <?= json_encode($jsonUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
        placeholderUrl: <?= json_encode(base_url('placeholder.png'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
        defaultCafeName: <?= json_encode($cafe['cafe_name'] ?: $username, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
        serviceWorkerUrl: <?= json_encode(site_url($username . '/sw.js'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
        pwaScope: <?= json_encode(site_url($username), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    };
</script>
<script src="<?= esc(base_url('vue.global.js')) ?>"></script>
<script src="<?= esc(base_url('app.js')) ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js"></script>
<script>
  Fancybox.bind("[data-fancybox]", {
    groupAttr: false,
    Hash: false,
    Carousel: {
      Toolbar: {
        display: {
          left: [],
          middle: [],
          right: ["close"],
        },
      },
    },
  });
</script>


</body>
</html>

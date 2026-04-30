(() => {
  const { createApp, nextTick } = Vue;
  const config = window.MenuAppConfig ?? {};
  const uncategorizedGroup = {
    id: 'uncategorized',
    display_name: 'Другое',
    sort_order: Number.MAX_SAFE_INTEGER,
  };

  createApp({
    data() {
      return {
        isLoading: true,
        errorMessage: '',
        selectedCategoryId: 'all',
        deferredInstallPrompt: null,
        canInstall: false,
        isAppInstalled: false,
        cafe: {
          name: config.defaultCafeName ?? 'Меню ресторана',
          slogan: '',
          currency: 'UZS',
          extra_fee: {
            enabled: false,
            type: null,
            value: null,
            translations: {},
          },
        },
        meta: {
          username: '',
          version: 0,
          updated_at: '',
        },
        categories: [],
        items: [],
        selectedLanguage: '',
        selectedItems: {},
        isCartOpen: false,
      };
    },
    computed: {
      isAllFoodSelected() {
        return this.selectedCategoryId === 'all';
      },
      filteredItems() {
        if (this.isAllFoodSelected) {
          return this.items;
        }

        return this.items.filter((item) => item.category_id === this.selectedCategoryId);
      },
      groupedItems() {
        const categoryMap = new Map(
          this.categories.map((category) => [
            category.id,
            {
              id: category.id,
              display_name: this.categoryName(category),
              sort_order: category.sort_order ?? 0,
              items: [],
            },
          ]),
        );

        this.items.forEach((item) => {
          const categoryId = item.category_id;
          const group = categoryMap.get(categoryId) ?? this.ensureUncategorizedGroup(categoryMap);
          group.items.push(item);
        });

        return [...categoryMap.values()]
          .filter((group) => group.items.length > 0)
          .sort((left, right) => left.sort_order - right.sort_order);
      },
      cafeName() {
        return this.cafe.name || config.defaultCafeName || 'Меню ресторана';
      },
      cafeSlogan() {
        return String(this.cafe.slogan ?? '').trim();
      },
      currencyLabel() {
        return this.cafe.currency || 'UZS';
      },
      updatedAtLabel() {
        return this.formatUpdatedAt(this.meta.updated_at);
      },
      placeholderUrl() {
        return config.placeholderUrl || '';
      },
      availableLanguages() {
        return this.meta.languages ?? [];
      },
      defaultLanguage() {
        return this.meta.default_language || 'en';
      },
      currentDirection() {
        const selected = this.availableLanguages.find((language) => language.code === this.selectedLanguage);

        return selected?.dir || 'ltr';
      },
      showLanguageSwitcher() {
        return this.availableLanguages.length > 1;
      },
      cartItems() {
        return this.items
          .map((item) => ({
            ...item,
            quantity: this.itemQuantity(item.id),
          }))
          .filter((item) => item.quantity > 0);
      },
      cartItemCount() {
        return this.cartItems.reduce((total, item) => total + item.quantity, 0);
      },
      cartHasItems() {
        return this.cartItemCount > 0;
      },
      extraFeeConfig() {
        return this.cafe.extra_fee ?? {
          enabled: false,
          type: null,
          value: null,
          translations: {},
        };
      },
      extraFeeEnabled() {
        return this.extraFeeConfig.enabled === true;
      },
      cartSubtotal() {
        return this.cartItems.reduce((total, item) => {
          return total + Number(item.price ?? 0) * item.quantity;
        }, 0);
      },
      cartFeeAmount() {
        if (!this.cartHasItems || !this.extraFeeEnabled) {
          return 0;
        }

        const feeValue = Number(this.extraFeeConfig.value ?? 0);

        if (!Number.isFinite(feeValue) || feeValue <= 0) {
          return 0;
        }

        if (this.extraFeeConfig.type === 'fixed') {
          return this.roundCurrency(feeValue);
        }

        if (this.extraFeeConfig.type === 'percent') {
          return this.roundCurrency((this.cartSubtotal * feeValue) / 100);
        }

        return 0;
      },
      cartGrandTotal() {
        return this.roundCurrency(this.cartSubtotal + this.cartFeeAmount);
      },
      showCartFeeBreakdown() {
        return this.extraFeeEnabled && this.cartHasItems;
      },
      cartFeeLabel() {
        return this.resolveText(this.extraFeeConfig.translations, 'label') || 'Доп. сбор';
      },
      cartBarTitle() {
        if (!this.cartHasItems) {
          return 'Корзина пуста';
        }

        return `Выбрано позиций: ${this.cartItemCount}`;
      },
      cartBarCaption() {
        return this.cartHasItems
          ? 'Покажите заказ официанту'
          : 'Выберите блюда';
      },
    },
    mounted() {
      this.isAppInstalled = this.detectStandaloneMode();
      window.addEventListener('beforeinstallprompt', this.handleBeforeInstallPrompt);
      window.addEventListener('appinstalled', this.handleAppInstalled);
      document.addEventListener('keydown', this.handleDocumentKeydown);
      this.registerServiceWorker();
      this.loadMenu();
    },
    beforeUnmount() {
      window.removeEventListener('beforeinstallprompt', this.handleBeforeInstallPrompt);
      window.removeEventListener('appinstalled', this.handleAppInstalled);
      document.removeEventListener('keydown', this.handleDocumentKeydown);
      document.body.classList.remove('body--cart-open');
    },
    methods: {
      async loadMenu() {
        try {
          const response = await fetch(config.jsonUrl, {
            headers: {
              Accept: 'application/json',
            },
          });

          if (!response.ok) {
            throw new Error('Не удалось загрузить меню.');
          }

          const data = await response.json();
          this.cafe = data.cafe ?? this.cafe;
          this.meta = data.meta ?? this.meta;
          this.selectedLanguage = this.pickInitialLanguage();
          this.categories = this.normalizeCategories(data.categories ?? []);
          this.items = this.normalizeItems(data.items ?? []);
          this.errorMessage = '';
        } catch (error) {
          this.errorMessage = error instanceof Error ? error.message : 'Не удалось загрузить меню.';
        } finally {
          this.isLoading = false;
        }
      },
      normalizeCategories(categories) {
        return [...categories].sort((left, right) => {
          return (left.sort_order ?? 0) - (right.sort_order ?? 0);
        });
      },
      normalizeItems(items) {
        return [...items]
          .filter((item) => item && item.id !== undefined && item.id !== null)
          .sort((left, right) => {
            const sortOrderDiff = (left.sort_order ?? 0) - (right.sort_order ?? 0);

            if (sortOrderDiff !== 0) {
              return sortOrderDiff;
            }

            return Number(left.id ?? 0) - Number(right.id ?? 0);
          });
      },
      storageKey() {
        return `menu-language:${this.meta.username || 'default'}`;
      },
      pickInitialLanguage() {
        const availableCodes = this.availableLanguages.map((language) => language.code);
        const storedLanguage = window.localStorage.getItem(this.storageKey());

        if (storedLanguage && availableCodes.includes(storedLanguage)) {
          return storedLanguage;
        }

        const browserLanguages = Array.isArray(navigator.languages) && navigator.languages.length > 0
          ? navigator.languages
          : [navigator.language].filter(Boolean);

        for (const browserLanguage of browserLanguages) {
          const normalized = String(browserLanguage).toLowerCase().split('-')[0];

          if (availableCodes.includes(normalized)) {
            return normalized;
          }
        }

        return this.defaultLanguage;
      },
      resolveText(translations, field) {
        if (!translations || typeof translations !== 'object') {
          return '';
        }

        const selectedValue = translations[this.selectedLanguage]?.[field];

        if (typeof selectedValue === 'string' && selectedValue.trim() !== '') {
          return selectedValue;
        }

        const fallbackValue = translations[this.defaultLanguage]?.[field];

        if (typeof fallbackValue === 'string' && fallbackValue.trim() !== '') {
          return fallbackValue;
        }

        return '';
      },
      categoryName(category) {
        return this.resolveText(category.translations, 'name') || '...';
      },
      categoryIcon(category) {
        return typeof category?.icon_url === 'string' && category.icon_url.trim() !== ''
          ? category.icon_url
          : '';
      },
      itemName(item) {
        return this.resolveText(item.translations, 'name') || '...';
      },
      itemDescription(item) {
        return this.resolveText(item.translations, 'description');
      },
      selectCategory(categoryId) {
        this.selectedCategoryId = categoryId;
      },
      ensureUncategorizedGroup(categoryMap) {
        if (!categoryMap.has(uncategorizedGroup.id)) {
          categoryMap.set(uncategorizedGroup.id, {
            ...uncategorizedGroup,
            items: [],
          });
        }

        return categoryMap.get(uncategorizedGroup.id);
      },
      itemQuantity(itemId) {
        return this.selectedItems[itemId] ?? 0;
      },
      incrementItem(itemId) {
        this.selectedItems[itemId] = this.itemQuantity(itemId) + 1;
      },
      decrementItem(itemId) {
        const nextQuantity = this.itemQuantity(itemId) - 1;

        if (nextQuantity <= 0) {
          delete this.selectedItems[itemId];
          return;
        }

        this.selectedItems[itemId] = nextQuantity;
      },
      removeItem(itemId) {
        delete this.selectedItems[itemId];
      },
      async registerServiceWorker() {
        if (!('serviceWorker' in navigator) || !config.serviceWorkerUrl) {
          return;
        }

        try {
          await navigator.serviceWorker.register(config.serviceWorkerUrl, {
            scope: config.pwaScope || undefined,
          });
        } catch (error) {
          console.error('Не удалось зарегистрировать service worker.', error);
        }
      },
      handleBeforeInstallPrompt(event) {
        event.preventDefault();
        this.deferredInstallPrompt = event;
        this.canInstall = true;
      },
      async promptInstall() {
        if (!this.deferredInstallPrompt) {
          return;
        }

        await this.deferredInstallPrompt.prompt();
        await this.deferredInstallPrompt.userChoice;
        this.deferredInstallPrompt = null;
        this.canInstall = false;
      },
      handleAppInstalled() {
        this.deferredInstallPrompt = null;
        this.canInstall = false;
        this.isAppInstalled = true;
      },
      detectStandaloneMode() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
      },
      openCart() {
        if (!this.cartHasItems) {
          return;
        }

        this.isCartOpen = true;
        document.body.classList.add('body--cart-open');

        nextTick(() => {
          this.$refs.cartDialog?.focus();
        });
      },
      closeCart() {
        this.isCartOpen = false;
        document.body.classList.remove('body--cart-open');
      },
      handleDocumentKeydown(event) {
        if (event.key === 'Escape' && this.isCartOpen) {
          this.closeCart();
        }
      },
      persistLanguageSelection() {
        if (!this.selectedLanguage) {
          return;
        }

        window.localStorage.setItem(this.storageKey(), this.selectedLanguage);
      },
      itemImage(item) {
        return item.image_url || this.placeholderUrl || '';
      },
      formatPrice(price) {
        const numericPrice = Number(price ?? 0);

        if (!Number.isFinite(numericPrice)) {
          return '0';
        }

        return new Intl.NumberFormat('en-US', {
          maximumFractionDigits: 2,
        }).format(numericPrice);
      },
      roundCurrency(value) {
        const numericValue = Number(value ?? 0);

        if (!Number.isFinite(numericValue)) {
          return 0;
        }

        return Math.round(numericValue * 100) / 100;
      },
      formatUpdatedAt(value) {
        if (!value) {
          return 'Неизвестно';
        }

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
          return 'Неизвестно';
        }

        return new Intl.DateTimeFormat('ru-RU', {
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          hour12: false,
        }).format(date);
      },
    },
    watch: {
      selectedLanguage() {
        this.persistLanguageSelection();
      },
    },
  }).mount('#menuApp');
})();

// resources/js/components/product-cart.js

document.addEventListener('alpine:init', () => {
    Alpine.data('cartHandler', (variations = []) => ({
        // Состояние компонента
        selectedVariation: null,
        loading: false,
        alertTimeout: null,
        isInWishlist: false,

        // Уведомления
        variationAlert: {
            show: false,
            type: 'info',
            message: ''
        },

        // Инициализация
        init() {
            // Автоматически выбираем первую вариацию если есть
            if (variations && variations.length > 0) {
                this.selectedVariation = variations[0].id;
            }

            // Проверяем статус wishlist
            this.checkWishlistStatus();
        },

        // Управление уведомлениями
        showVariationAlert(type, message) {
            this.variationAlert = {
                show: true,
                type: type,
                message: message
            };

            // Автоматически скрыть через 4 секунды
            this.clearAlertTimeout();
            this.alertTimeout = setTimeout(() => {
                this.hideVariationAlert();
            }, 4000);
        },

        hideVariationAlert() {
            this.variationAlert.show = false;
            this.clearAlertTimeout();
        },

        clearAlertTimeout() {
            if (this.alertTimeout) {
                clearTimeout(this.alertTimeout);
                this.alertTimeout = null;
            }
        },

        // Выбор вариации
        selectVariation(variationId) {
            this.selectedVariation = variationId;
            this.hideVariationAlert();
        },

        // Проверка статуса wishlist
        checkWishlistStatus() {
            const { productId } = this.getElementData();
            if (productId) {
                this.isInWishlist = localStorage.getItem(`wishlist_${productId}`) === 'true';
            }
        },

        // Получение данных элемента
        getElementData() {
            const element = this.$el.closest('[data-product-cart]') || this.$el;
            return {
                nonce: element.dataset.nonce || '',
                ajaxUrl: element.dataset.ajaxUrl || '',
                productId: element.dataset.productId || ''
            };
        },

        // Валидация перед добавлением в корзину
        validateAddToCart(variationId) {
            if (!variationId) {
                this.showVariationAlert('warning', 'Please select a variation before adding to cart.');
                return false;
            }

            const variation = variations.find(v => v.id === variationId);
            if (!variation) {
                this.showVariationAlert('error', 'The selected variation is not available.');
                return false;
            }

            if (!variation.is_in_stock) {
                this.showVariationAlert('warning', 'The selected variation is out of stock.');
                return false;
            }

            if (!variation.is_purchasable) {
                this.showVariationAlert('error', 'This product is not available for purchase.');
                return false;
            }

            return { isValid: true, variation };
        },

        // Построение FormData для запроса
        buildFormData(variationId, variation) {
            const { nonce, productId } = this.getElementData();
            const formData = new FormData();

            formData.append('_wpnonce', nonce);
            formData.append('action', 'woocommerce_ajax_add_to_cart');
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            // Для вариативных товаров
            if (variation) {
                formData.append('variation_id', variationId);

                // Добавляем атрибуты вариации
                if (variation.raw_attributes) {
                    Object.entries(variation.raw_attributes).forEach(([key, value]) => {
                        if (value) {
                            formData.append(key, value);
                        }
                    });
                }
            }

            return formData;
        },

        // Отправка AJAX запроса
        async sendCartRequest(formData) {
            const { ajaxUrl } = this.getElementData();

            const response = await fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        },

        // Обработка успешного добавления
        handleAddToCartSuccess(result) {
            console.log('Product added to cart:', result);
            this.showVariationAlert('success', 'Product successfully added to cart!');

            // Если в ответе есть cart_count, используем его
            if (result.cart_count !== undefined) {
                this.updateCartCountDirect(result.cart_count);
            } else {
                // Иначе делаем отдельный запрос
                this.updateCartCount();
            }

            // Диспатчим кастомное событие
            this.$dispatch('product-added-to-cart', {
                variationId: this.selectedVariation,
                result: result
            });

            this.updateBrowserState();
        },

        updateCartCountDirect(count) {
            document.dispatchEvent(new CustomEvent('cart-count-updated', {
                detail: { count: parseInt(count) }
            }));
        },

        // Обновление счетчика корзины
        updateCartCount() {
            if (window.updateCartCount && typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }

            // Альтернативный способ через событие
            document.dispatchEvent(new CustomEvent('cart-updated'));
        },

        // Обновление состояния браузера
        updateBrowserState() {
            if (this.selectedVariation && window.history && window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.set('variation_id', this.selectedVariation);
                window.history.replaceState(null, '', url);
            }
        },

        // Основная функция добавления в корзину
        async addToCart(variationId = null) {
            // Скрываем предыдущие уведомления
            this.hideVariationAlert();

            // Определяем ID для добавления
            const targetVariationId = variationId || this.selectedVariation;

            // Валидация
            const validation = this.validateAddToCart(targetVariationId);
            if (!validation || !validation.isValid) {
                return;
            }

            // Устанавливаем состояние загрузки
            this.loading = true;
            this.showVariationAlert('info', 'Adding product to cart...');

            try {
                // Строим данные для отправки
                const formData = this.buildFormData(targetVariationId, validation.variation);

                // Отправляем запрос
                const result = await this.sendCartRequest(formData);

                console.log('[Cart] Кнопка нажата');
                console.log('[Cart] selectedVariation:', this.selectedVariation);
                console.log('[Cart] ajaxUrl:', window.searchAjax.ajax_url);
                console.log('[Cart] nonce:', document.querySelector('[data-product-cart]').dataset.nonce);
                console.log('[Cart] FormData:', [...formData.entries()]);

                // Обрабатываем результат
                if (result.error) {
                    this.showVariationAlert('error', result.error);
                } else {
                    this.handleAddToCartSuccess(result);
                }

            } catch (error) {
                console.error('Error adding to cart:', error);
                this.showVariationAlert('error', 'There was an error adding the item to your cart. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        // Добавление в избранное
        async toggleWishlist() {
            const { productId } = this.getElementData();

            if (!productId) {
                console.error('Product ID not found');
                return;
            }

            try {
                const newState = !this.isInWishlist;

                localStorage.setItem(`wishlist_${productId}`, newState);
                this.isInWishlist = newState;

                const message = newState
                    ? 'The product has been added to your favorites'
                    : 'The product has been removed from favorites';

                showGlobalAlert('success', message);

                // Диспатчим событие
                this.$dispatch('wishlist-updated', {
                    productId: productId,
                    isInWishlist: newState
                });

            } catch (error) {
                console.error('Wishlist error:', error);
                showGlobalAlert('error', 'Error updating favorites');
            }
        },

        // Поделиться товаром
        async shareProduct() {
            const shareData = {
                title: document.title,
                text: 'Check out this product',
                url: window.location.href
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    // Fallback - копируем в буфер обмена
                    await navigator.clipboard.writeText(window.location.href);
                    showGlobalAlert('success', 'Link copied to clipboard');
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Share error:', error);
                    showGlobalAlert('error', 'Failed to share product');
                }
            }
        }
    }));

      Alpine.data('cartCounter', () => ({
        count: 0,

        init() {
            this.count = parseInt(document.querySelector('[data-cart-count]')?.textContent || '0');

            // Слушаем прямое обновление счетчика
            document.addEventListener('cart-count-updated', (e) => {
                this.count = e.detail.count;
            });

            // Слушаем события для запроса счетчика
            document.addEventListener('cart-updated', () => {
                this.fetchCartCount();
            });

            document.body.addEventListener('added_to_cart', () => {
                this.fetchCartCount();
            });
        },

        async fetchCartCount() {
            try {
                const formData = new FormData();
                formData.append('action', 'get_cart_count');

                const response = await fetch(window.wc_add_to_cart_params?.ajax_url || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.count = parseInt(result.data.count);
                }
            } catch (error) {
                console.error('Error fetching cart count:', error);
            }
        }
  }));
});

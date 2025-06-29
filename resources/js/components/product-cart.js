// resources/scripts/components/product-cart.js

document.addEventListener('alpine:init', () => {
    Alpine.data('productCart', () => ({
        // Состояние
        selectedVariation: null,
        loading: false,
        isInWishlist: false,
        productData: null,
        alertTimeout: null,

        // Локальные уведомления
        variationAlert: {
            show: false,
            type: 'info',
            message: ''
        },

        // Инициализация
        init(product) {
            this.productData = product;

            // Автоматически выбираем первую вариацию для вариативных товаров
            if (product.type === 'variable' && product.variations?.length > 0) {
                this.selectedVariation = product.variations[0].id;
            }

            // Проверяем, находится ли товар в избранном
            this.checkWishlistStatus();
        },

        // Выбор вариации
        selectVariation(variationId) {
            this.selectedVariation = variationId;
            this.hideAlert();
        },

        // Управление уведомлениями
        showAlert(type, message) {
            this.variationAlert = {
                show: true,
                type: type,
                message: message
            };

            // Автоматически скрыть через 4 секунды
            this.clearAlertTimeout();
            this.alertTimeout = setTimeout(() => {
                this.hideAlert();
            }, 4000);
        },

        hideAlert() {
            this.variationAlert.show = false;
            this.clearAlertTimeout();
        },

        clearAlertTimeout() {
            if (this.alertTimeout) {
                clearTimeout(this.alertTimeout);
                this.alertTimeout = null;
            }
        },

        // Добавление в корзину
        async addToCart(productId = null) {
            this.hideAlert();

            // Определяем ID товара
            const targetId = productId || this.selectedVariation;

            if (!targetId) {
                this.showAlert('warning', 'Пожалуйста, выберите вариацию перед добавлением в корзину.');
                return;
            }

            // Находим данные вариации
            let variation = null;
            if (this.productData.type === 'variable') {
                variation = this.productData.variations?.find(v => v.id === targetId);
                if (!variation) {
                    this.showAlert('error', 'Выбранная вариация недоступна.');
                    return;
                }
            }

            // Проверяем доступность
            if (variation && !variation.is_purchasable) {
                this.showAlert('error', 'Этот товар в данный момент недоступен для покупки.');
                return;
            }

            this.loading = true;
            this.showAlert('info', 'Добавляем товар в корзину...');

            try {
                const formData = this.buildFormData(targetId, variation);
                const result = await this.sendToCart(formData);

                if (result.error) {
                    this.showAlert('error', result.error);
                } else {
                    this.showAlert('success', 'Товар успешно добавлен в корзину!');
                    this.updateCartUI(result);
                }
            } catch (error) {
                console.error('Ошибка добавления в корзину:', error);
                this.showAlert('error', 'Произошла ошибка при добавлении товара в корзину. Попробуйте еще раз.');
            } finally {
                this.loading = false;
            }
        },

        // Построение FormData для запроса
        buildFormData(productId, variation) {
            const formData = new FormData();
            const element = this.$el;

            formData.append('_wpnonce', element.dataset.nonce);
            formData.append('action', 'woocommerce_ajax_add_to_cart');
            formData.append('product_id', this.productData.id);
            formData.append('quantity', 1);

            if (variation) {
                formData.append('variation_id', productId);
                // Добавляем атрибуты вариации
                Object.entries(variation.raw_attributes || {}).forEach(([key, value]) => {
                    if (value) {
                        formData.append(key, value);
                    }
                });
            }

            return formData;
        },

        // Отправка запроса
        async sendToCart(formData) {
            const element = this.$el;
            const response = await fetch(element.dataset.ajaxUrl, {
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

        // Обновление UI после добавления в корзину
        updateCartUI(result) {
            // Обновить счетчик корзины
            if (window.updateCartCount && typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }

            // Диспатчим событие для других компонентов
            this.$dispatch('product-added-to-cart', {
                productId: this.productData.id,
                variationId: this.selectedVariation,
                result: result
            });
        },

        // Управление избранным
        async toggleWishlist() {
            try {
                this.isInWishlist = !this.isInWishlist;

                // Здесь можно добавить логику сохранения в localStorage или отправки на сервер
                localStorage.setItem(`wishlist_${this.productData.id}`, this.isInWishlist);

                const message = this.isInWishlist
                    ? 'Товар добавлен в избранное'
                    : 'Товар удален из избранного';

                this.showAlert('success', message);
            } catch (error) {
                console.error('Ошибка управления избранным:', error);
                this.isInWishlist = !this.isInWishlist; // Откатываем изменение
                this.showAlert('error', 'Ошибка при обновлении избранного');
            }
        },

        // Проверка статуса избранного
        checkWishlistStatus() {
            const wishlistStatus = localStorage.getItem(`wishlist_${this.productData.id}`);
            this.isInWishlist = wishlistStatus === 'true';
        },

        // Поделиться товаром
        async shareProduct() {
            const shareData = {
                title: this.productData.title,
                text: this.productData.short_description || this.productData.title,
                url: window.location.href
            };

            try {
                // Используем Web Share API если доступен
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    // Fallback - копируем ссылку в буфер обмена
                    await navigator.clipboard.writeText(window.location.href);
                    this.showAlert('success', 'Ссылка на товар скопирована в буфер обмена');
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Ошибка при попытке поделиться:', error);
                    this.showAlert('error', 'Не удалось поделиться товаром');
                }
            }
        }
    }));
});

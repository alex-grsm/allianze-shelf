// resources/js/main.js
import Alpine from 'alpinejs';

import viewportHeight from './utils/viewport-height';
import SwiperComponent from './components/swiper.js';
// import { productVariationsComponent } from './modules/woocommerce-cart';

window.Alpine = Alpine;
// Alpine.data('productVariations', productVariationsComponent);

function initApp() {
  Alpine.start();

  viewportHeight.init();
  SwiperComponent.init();

  // Слушаем события корзины WooCommerce
  document.body.addEventListener('wc_added_to_cart', (event) => {
    console.log('Товар добавлен в корзину:', event.detail);

    // Можно добавить дополнительные действия, например:
    // - Показать мини-корзину
    // - Обновить счетчик в шапке
    // - Показать уведомление
  });

  // Обработка событий выбора вариации
  document.body.addEventListener('variation-selected', (event) => {
    console.log('Выбрана вариация:', event.detail.variation);

    // Можно обновить изображение товара или другие элементы
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

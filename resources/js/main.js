// resources/js/main.js
import Alpine from 'alpinejs';

import { showGlobalAlert, hideGlobalAlert } from './helpers/alert.js';
import viewportHeight from './helpers/viewport-height.js';
import SwiperComponent from './components/swiper.js';
import './components/product-cart.js';
// import { productVariationsComponent } from './modules/woocommerce-cart';

window.Alpine = Alpine;
window.showGlobalAlert = showGlobalAlert;
window.hideGlobalAlert = hideGlobalAlert;
// window.cartHandler = cartHandler;


function initApp() {
  Alpine.start();

  // Инициализация твоих утилит и компонентов
  viewportHeight.init();
  SwiperComponent.init();

  // Тестовый алерт (можно убрать)
  // setTimeout(() => {
  //   showGlobalAlert('success', 'Система уведомлений готова!');
  // }, 1000);

}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

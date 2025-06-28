// resources/js/main.js
import Alpine from 'alpinejs';

import { showGlobalAlert } from './helpers/alert.js';
import viewportHeight from './helpers/viewport-height.js';
import SwiperComponent from './components/swiper.js';
// import { productVariationsComponent } from './modules/woocommerce-cart';

window.Alpine = Alpine;
window.showGlobalAlert = showGlobalAlert;
// window.cartHandler = cartHandler;


function initApp() {
  Alpine.start();

  // Инициализация твоих утилит и компонентов
  viewportHeight.init();
  SwiperComponent.init();

  // setTimeout(() => {
  //   showGlobalAlert('success', 'Тестовый алерт');
  // }, 1000);

}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

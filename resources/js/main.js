// resources/js/main.js
import Alpine from 'alpinejs';
import viewportHeight from './helpers/viewport-height.js';
import './components/product-cart.js';

import { showGlobalAlert, hideGlobalAlert } from './helpers/alert.js';
import SwiperComponent from './components/swiper.js';

window.Alpine = Alpine;
window.showGlobalAlert = showGlobalAlert;
window.hideGlobalAlert = hideGlobalAlert;

function initApp() {
  Alpine.start();

  viewportHeight.init();
  SwiperComponent.init();

}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

// resources/js/main.js
import Alpine from 'alpinejs';

import viewportHeight from './utils/viewport-height';
import SwiperComponent from './components/swiper.js';

window.Alpine = Alpine;

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

// resources/js/main.js
import viewportHeight from './utils/viewport-height';
import SwiperComponent from './components/swiper.js';


function initApp() {
  viewportHeight.init();
  SwiperComponent.init();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

// resources/js/components/swiper.js
import Swiper from 'swiper/bundle';

// Импортируем CSS
import 'swiper/css/bundle';

export default {
  init() {
    this.initSwipers();
  },

  initSwipers() {
    // Ищем все слайдеры с общим классом swiper или конкретными классами
    const swipers = document.querySelectorAll(
      '.hero-slider, .crop-cards-slider, .crop-concepts-slider'
    );

    swipers.forEach((slider) => {
      this.setupSwiper(slider);
    });
  },

  setupSwiper(sliderElement) {
    // Базовые настройки
    const config = {
      centeredSlides: false,
      spaceBetween: 16,
      // loop: true,
      breakpoints: {
        320: {
          slidesPerView: 1.3,
        },
        768: {
          slidesPerView: 2.3,
        },
        1024: {
          slidesPerView: 3.5,
        },
        1440: {
          slidesPerView: 4.78,
        },
      },
    };


    // if (sliderElement.classList.contains('crop-concepts-slider')) {
    //   Object.assign(config, {

    //   });
    // }

    // Настройки для hero-slider
    if (sliderElement.classList.contains('hero-slider')) {
      Object.assign(config, {
        slidesPerView: 1,
        effect: 'fade',
        fadeEffect: {
          crossFade: true,
        },
        // autoplay: {
        //   delay: 2000,
        //   disableOnInteraction: false,
        // },
        loop: true,
        speed: 1700,
        // pagination: {
        //   el: sliderElement.querySelector('.swiper-pagination'),
        //   clickable: true,
        //   bulletClass: 'swiper-pagination-bullet',
        //   bulletActiveClass: 'swiper-pagination-bullet-active',
        // },
        grabCursor: true,
        spaceBetween: 0,
        breakpoints: {},
      });
    }

    // Создаем экземпляр Swiper
    const swiper = new Swiper(sliderElement, config);

    // Возвращаем экземпляр для возможного дальнейшего использования
    return swiper;
  },
};

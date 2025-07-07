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
      '.hero-slider, .crop-cards-slider, .crop-concepts-slider, .asset-overview-slider'
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

    // Настройки для asset-overview-slider
    if (sliderElement.classList.contains('asset-overview-slider')) {
      Object.assign(config, {
        spaceBetween: 30,
        loop: false,
        grabCursor: true,
        slideToClickedSlide: true,

        // Адаптивные настройки для asset overview
        breakpoints: {
          320: {
            slidesPerView: 1,
            spaceBetween: 20,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 24,
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 30,
          },
          1280: {
            slidesPerView: 3,
            spaceBetween: 40,
          },
        },

        // Эффекты перехода
        speed: 600,

        // Дополнительные эффекты при свайпе
        on: {
          init: function() {
            // Инициализируем эффекты при загрузке
            this.emit('progress', this.progress);
          },

          setTransition: function (duration) {
            const slides = this.slides;
            slides.forEach((slide) => {
              slide.style.transition = `${duration}ms`;
              const img = slide.querySelector('img');
              if (img) {
                img.style.transition = `transform ${duration}ms ease-out`;
              }
            });
          },

          progress: function (progress) {
            const slides = this.slides;
            slides.forEach((slide, index) => {
              const slideProgress = slide.progress || 0;
              const absProgress = Math.abs(slideProgress);

              // Масштабирование слайдов
              let scale = 1 - absProgress * 0.15;
              scale = Math.max(scale, 0.8);

              // Прозрачность
              let opacity = 1 - absProgress * 0.4;
              opacity = Math.max(opacity, 0.6);

              slide.style.transform = `scale(${scale})`;
              slide.style.opacity = opacity;

              // Добавляем z-index для активного слайда
              if (absProgress < 0.1) {
                slide.style.zIndex = 10;
              } else {
                slide.style.zIndex = 1;
              }
            });
          },

          slideChange: function() {
            // Обновляем эффекты при смене слайда
            this.emit('progress', this.progress);
          }
        },
      });
    }

    // if (sliderElement.classList.contains('crop-concepts-slider')) {
    //   Object.assign(config, {

    //   });
    // }

    // Создаем экземпляр Swiper
    const swiper = new Swiper(sliderElement, config);

    // Дополнительная обработка для asset-overview-slider
    if (sliderElement.classList.contains('asset-overview-slider')) {
      // Инициализируем эффекты для первого слайда
      setTimeout(() => {
        swiper.emit('progress', swiper.progress);
      }, 100);

      // Добавляем hover эффекты для asset слайдов
      const slides = sliderElement.querySelectorAll('.asset-slide');
      slides.forEach((slide) => {
        slide.addEventListener('mouseenter', () => {
          const img = slide.querySelector('img');
          if (img) {
            img.style.transform = 'scale(1.05)';
          }
        });

        slide.addEventListener('mouseleave', () => {
          const img = slide.querySelector('img');
          if (img) {
            img.style.transform = 'scale(1)';
          }
        });
      });
    }

    // Возвращаем экземпляр для возможного дальнейшего использования
    return swiper;
  },
};

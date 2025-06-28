/**
 * Утилита для корректной работы с высотой viewport на мобильных устройствах
 * Решает проблему с динамической высотой viewport при появлении/скрытии адресной строки браузера
 *
 * @module ViewportHeight
 */

class ViewportHeight {
  constructor() {
    this.resizeHandler = null;
    this.orientationHandler = null;
    this.debounceTimer = null;
  }

  /**
   * Инициализация утилиты
   */
  init() {
    // Устанавливаем начальное значение
    this.updateViewportHeight();

    // Создаем обработчики с привязкой контекста
    this.resizeHandler = this.debounce(() => this.updateViewportHeight(), 100);
    this.orientationHandler = () => {
      // Задержка необходима для корректного получения размеров после поворота
      setTimeout(() => this.updateViewportHeight(), 200);
    };

    // Добавляем слушатели событий
    this.addEventListeners();
  }

  /**
   * Обновляет CSS переменную с актуальной высотой viewport
   */
  updateViewportHeight() {
    try {
      // Используем visualViewport API если доступен (более точный на мобильных)
      const viewportHeight = window.visualViewport?.height || window.innerHeight;

      // Проверяем, изменилась ли высота (оптимизация)
      const currentHeight = getComputedStyle(document.documentElement)
        .getPropertyValue('--full-vh');

      if (currentHeight === `${viewportHeight}px`) {
        return; // Высота не изменилась, пропускаем обновление
      }

      // Устанавливаем CSS переменную на :root (html элемент)
      // Это стандартная практика для глобальных CSS переменных
      document.documentElement.style.setProperty('--full-vh', `${viewportHeight}px`);

      // Диспатчим кастомное событие для других компонентов
      window.dispatchEvent(new CustomEvent('viewportHeightUpdated', {
        detail: { height: viewportHeight }
      }));
    } catch (error) {
      console.error('Ошибка при обновлении высоты viewport:', error);
    }
  }

  /**
   * Добавляет обработчики событий
   */
  addEventListeners() {
    // Используем visualViewport API для более точного отслеживания изменений
    if (window.visualViewport) {
      window.visualViewport.addEventListener('resize', this.resizeHandler);
      window.visualViewport.addEventListener('scroll', this.resizeHandler);
    } else {
      // Fallback для старых браузеров
      window.addEventListener('resize', this.resizeHandler);
    }

    // Обработка поворота экрана
    window.addEventListener('orientationchange', this.orientationHandler);

    // Дополнительно отслеживаем изменение размера через ResizeObserver
    if ('ResizeObserver' in window) {
      this.resizeObserver = new ResizeObserver(this.resizeHandler);
      this.resizeObserver.observe(document.documentElement);
    }
  }

  /**
   * Удаляет обработчики событий
   */
  removeEventListeners() {
    if (window.visualViewport) {
      window.visualViewport.removeEventListener('resize', this.resizeHandler);
      window.visualViewport.removeEventListener('scroll', this.resizeHandler);
    } else {
      window.removeEventListener('resize', this.resizeHandler);
    }

    window.removeEventListener('orientationchange', this.orientationHandler);

    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }

    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer);
    }
  }

  /**
   * Debounce функция для оптимизации частых вызовов
   * @param {Function} func - Функция для debounce
   * @param {number} wait - Задержка в миллисекундах
   * @returns {Function} Debounced функция
   */
  debounce(func, wait) {
    return (...args) => {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => func.apply(this, args), wait);
    };
  }

  /**
   * Деструктор - очищает ресурсы
   */
  destroy() {
    this.removeEventListeners();
    document.documentElement.style.removeProperty('--full-vh');
  }
}

// Экспортируем синглтон
const viewportHeight = new ViewportHeight();

export default {
  init: () => viewportHeight.init(),
  update: () => viewportHeight.updateViewportHeight(),
  destroy: () => viewportHeight.destroy(),
  getInstance: () => viewportHeight
};

// Использование в CSS:
// height: 100vh; /* fallback */
// height: var(--full-vh);

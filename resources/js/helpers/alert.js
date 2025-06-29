export function showGlobalAlert(type = 'info', message = '') {
  const alertEl = document.getElementById('global-alert');
  if (!alertEl) return;

  const messageEl = document.getElementById('alert-message');
  const iconEl = document.getElementById('alert-icon');

  if (messageEl) {
    messageEl.innerHTML = message;
  }

  // Удаляем старые классы цвета
  alertEl.classList.remove(
    'text-green-50', 'bg-green-500',
    'text-blue-50', 'bg-blue-500',
    'text-yellow-50', 'bg-yellow-500',
    'text-red-50', 'bg-red-500',
    'hidden'
  );

  // Добавляем новые классы и иконки по типу
  switch (type) {
    case 'success':
      alertEl.classList.add('text-green-50', 'bg-green-500');
      if (iconEl) {
        iconEl.innerHTML = `
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
        `;
      }
      break;
    case 'info':
      alertEl.classList.add('text-blue-50', 'bg-blue-500');
      if (iconEl) {
        iconEl.innerHTML = `
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
        `;
      }
      break;
    case 'warning':
      alertEl.classList.add('text-yellow-50', 'bg-yellow-500');
      if (iconEl) {
        iconEl.innerHTML = `
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
        `;
      }
      break;
    case 'error':
      alertEl.classList.add('text-red-50', 'bg-red-500');
      if (iconEl) {
        iconEl.innerHTML = `
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
        `;
      }
      break;
    default:
      alertEl.classList.add('text-blue-50', 'bg-blue-500');
      if (iconEl) {
        iconEl.innerHTML = `
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
        `;
      }
  }

  // Показываем с анимацией
  alertEl.classList.remove('hidden', 'translate-x-full', 'opacity-0');
  alertEl.classList.add('translate-x-0', 'opacity-100');

  // Автоматически скрыть через 4 секунды
  clearTimeout(alertEl.hideTimeout);
  alertEl.hideTimeout = setTimeout(() => {
    hideGlobalAlert();
  }, 4000);
}

export function hideGlobalAlert() {
  const alertEl = document.getElementById('global-alert');
  if (!alertEl) return;

  // Скрываем с анимацией
  alertEl.classList.remove('translate-x-0', 'opacity-100');
  alertEl.classList.add('translate-x-full', 'opacity-0');

  // Полностью скрываем после анимации
  setTimeout(() => {
    alertEl.classList.add('hidden');
  }, 300);

  clearTimeout(alertEl.hideTimeout);
}

// Делаем функцию глобально доступной
if (typeof window !== 'undefined') {
  window.hideGlobalAlert = hideGlobalAlert;
}

export function showGlobalAlert(type = 'info', message = '') {
  const alertEl = document.getElementById('global-alert');
  if (!alertEl) return;

  alertEl.innerHTML = message;

  // Удаляем старые классы цвета
  alertEl.classList.remove(
    'text-green-50', 'bg-green-500',
    'text-blue-50', 'bg-blue-500',
    'text-yellow-50', 'bg-yellow-500',
    'text-red-50', 'bg-red-500',
    'hidden'
  );

  // Добавляем новые классы по типу
  switch (type) {
    case 'success':
      alertEl.classList.add('text-green-50', 'bg-green-500');
      break;
    case 'info':
      alertEl.classList.add('text-blue-50', 'bg-blue-500');
      break;
    case 'warning':
      alertEl.classList.add('text-yellow-50', 'bg-yellow-500');
      break;
    case 'error':
      alertEl.classList.add('text-red-50', 'bg-red-500');
      break;
    default:
      alertEl.classList.add('text-blue-50', 'bg-blue-500');
  }

  alertEl.classList.remove('hidden');

  clearTimeout(alertEl.hideTimeout);
  alertEl.hideTimeout = setTimeout(() => {
    alertEl.classList.add('hidden');
  }, 4000);
}

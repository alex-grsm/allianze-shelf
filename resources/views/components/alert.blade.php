@props([
  'type' => 'info',
  'message' => '',
])

@php
  $classes = match ($type) {
    'success' => 'text-green-50 bg-green-500',
    'info'    => 'text-blue-50 bg-blue-500',
    'warning' => 'text-yellow-50 bg-yellow-500',
    'error'   => 'text-red-50 bg-red-500',
    default   => 'text-blue-50 bg-blue-500',
  };
@endphp

<div
  id="global-alert"
  {{ $attributes->merge(['class' => "hidden fixed top-10 right-10 z-[999] cursor-pointer max-w-xs rounded-lg shadow-lg select-none transition-all duration-300 transform translate-x-full opacity-0 {$classes}"]) }}
  role="alert"
  onclick="hideGlobalAlert()"
>
  <div class="flex items-center p-4">
    {{-- Иконка --}}
    <div class="flex-shrink-0 mr-3" id="alert-icon">
      {{-- Иконки будут добавляться через JavaScript --}}
    </div>

    {{-- Сообщение --}}
    <div class="flex-1 font-medium" id="alert-message">
      {!! $message !!}
    </div>

    {{-- Кнопка закрытия --}}
    <div class="flex-shrink-0 ml-3">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
      </svg>
    </div>
  </div>
</div>

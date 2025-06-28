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
  {{ $attributes->merge(['class' => "hidden fixed top-10 right-10 z-[999] cursor-pointer max-w-xs px-4 py-3 rounded shadow-lg select-none {$classes}"]) }}
  role="alert"
  onclick="this.classList.add('hidden')"
>
  {!! $message !!}
</div>

@props(['name', 'class' => 'width-6 height-6'])

@php
$iconPath = public_path("icons/{$name}.svg");
$svgContent = file_exists($iconPath) ? file_get_contents($iconPath) : null;
@endphp

@if($svgContent)
  {!! preg_replace('/<svg/', '<svg ' . $attributes->merge(['class' => $class])->toHtml(), $svgContent, 1) !!}
@else
  <span class="text-red-500">Icon "{{ $name }}" not found</span>
@endif

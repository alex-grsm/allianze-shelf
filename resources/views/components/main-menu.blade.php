@php
use App\Helpers\MenuHelper;
$menuItems = MenuHelper::getMainMenu();
@endphp

<nav class="hidden md:block" x-data="{ activeDropdown: null }">
  <div class="flex items-baseline space-x-7">

    @foreach($menuItems as $item)
      <div class="menu-item relative"
           @mouseenter="activeDropdown = '{{ $item['slug'] }}'"
           @mouseleave="activeDropdown = null">

        <a href="{{ $item['url'] }}"
           class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200 hover:text-gray-300">
          {!! html_entity_decode($item['name']) !!}
          @if($item['has_subcategories'])
            <div class="ml-0.5 size-4 flex items-center justify-center">
              <x-svg-icon name="angle-bottom" class="transition-transform duration-200"
                         x-bind:class="activeDropdown === '{{ $item['slug'] }}' ? 'rotate-180' : ''" />
            </div>
          @endif
        </a>

        @if($item['has_subcategories'])
          {{-- Подменю --}}
          <div class="absolute top-full left-0 w-48 bg-white text-black shadow-lg rounded-md overflow-hidden transition-all duration-200 transform origin-top"
               x-show="activeDropdown === '{{ $item['slug'] }}'"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
               x-transition:enter-end="opacity-100 scale-100 translate-y-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="opacity-100 scale-100 translate-y-0"
               x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
               x-cloak>

            @foreach($item['subcategories'] as $index => $subcategory)
              <a href="{{ $subcategory['url'] }}"
                 class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 {{ $index < count($item['subcategories']) - 1 ? 'border-b border-gray-100' : '' }}">
                {!! html_entity_decode($subcategory['name']) !!}
                @if($subcategory['count'] > 0)
                  <span class="text-gray-500 text-xs ml-1">({{ $subcategory['count'] }})</span>
                @endif
              </a>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach

  </div>
</nav>

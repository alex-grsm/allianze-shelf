@php
use App\Helpers\MenuHelper;
$menuItems = MenuHelper::getMobileMenu();
@endphp

<div class="md:hidden" x-data="{ mobileMenuOpen: false }">
  <div class="flex items-center space-x-2">
    {{-- Мобильный поиск --}}
    @include('components.header-search')

    {{-- Мобильное меню --}}
    <button class="text-white hover:text-gray-300 p-2 rounded-md"
            @click="mobileMenuOpen = !mobileMenuOpen"
            aria-label="Меню">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>
  </div>

  {{-- Мобильное меню --}}
  <div class="absolute top-full left-0 right-0 bg-gray-900 shadow-lg transition-all duration-300 transform origin-top"
       x-show="mobileMenuOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
       x-transition:enter-end="opacity-100 scale-100 translate-y-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 scale-100 translate-y-0"
       x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
       @click.outside="mobileMenuOpen = false"
       x-cloak>

    <div class="px-4 pt-2 pb-3 space-y-1">
      @foreach($menuItems as $item)
        <div x-data="{ expanded: false }" class="border-b border-gray-700 last:border-b-0">
          <div class="flex items-center justify-between">
            <a href="{{ $item['url'] }}"
               class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium flex-1">
              {!! html_entity_decode($item['name']) !!}
            </a>

            @if($item['has_subcategories'])
              <button @click="expanded = !expanded"
                      class="text-white hover:text-gray-300 p-2 ml-2">
                <svg class="w-4 h-4 transition-transform duration-200"
                     :class="expanded ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
            @endif
          </div>

          @if($item['has_subcategories'])
            <div x-show="expanded"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 max-h-0"
                 x-transition:enter-end="opacity-100 max-h-96"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 max-h-96"
                 x-transition:leave-end="opacity-0 max-h-0"
                 class="overflow-hidden pl-4 pb-2"
                 x-cloak>
              @foreach($item['subcategories'] as $subcategory)
                <a href="{{ $subcategory['url'] }}"
                   class="text-gray-300 hover:text-white block px-3 py-1 text-sm">
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
  </div>
</div>

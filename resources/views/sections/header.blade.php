<header class="header bg-black text-white xl:min-h-20 flex items-center absolute top-0 left-0 right-0 z-50">
  <div class="container">
    <div class="flex items-center justify-between h-16">

      {{-- Логотип --}}
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <a href="{{ home_url('/') }}" class="flex items-center">
            <img src="{{ Vite::asset('resources/images/logo.svg') }}">
          </a>
        </div>
      </div>

      {{-- Главное меню --}}
      <nav class="hidden md:block" x-data="{ activeDropdown: null }">
        <div class="flex items-baseline space-x-7">

          {{-- Products --}}
          <div class="menu-item relative"
               @mouseenter="activeDropdown = 'products'"
               @mouseleave="activeDropdown = null">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200 hover:text-gray-300">
              Products
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <x-svg-icon name="angle-bottom" class="transition-transform duration-200"
                           x-bind:class="activeDropdown === 'products' ? 'rotate-180' : ''" />
              </div>
            </a>

            {{-- Подменю Products --}}
            <div class="absolute top-full left-0 w-48 bg-white text-black shadow-lg rounded-md overflow-hidden transition-all duration-200 transform origin-top"
                 x-show="activeDropdown === 'products'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 x-cloak>
              <a href="{{ get_term_link(17, 'product_cat') }}" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">P&C</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Life</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150">Health</a>
            </div>
          </div>

          {{-- Channels --}}
          <div class="menu-item relative"
               @mouseenter="activeDropdown = 'channels'"
               @mouseleave="activeDropdown = null">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200 hover:text-gray-300">
              Channels
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <x-svg-icon name="angle-bottom" class="transition-transform duration-200"
                           x-bind:class="activeDropdown === 'channels' ? 'rotate-180' : ''" />
              </div>
            </a>

            {{-- Подменю Channels (пример) --}}
            <div class="absolute top-full left-0 w-48 bg-white text-black shadow-lg rounded-md overflow-hidden transition-all duration-200 transform origin-top"
                 x-show="activeDropdown === 'channels'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 x-cloak>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Online</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Offline</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150">Partners</a>
            </div>
          </div>

          {{-- Campaigns --}}
          <div class="menu-item relative"
               @mouseenter="activeDropdown = 'campaigns'"
               @mouseleave="activeDropdown = null">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200 hover:text-gray-300">
              Campaigns
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <x-svg-icon name="angle-bottom" class="transition-transform duration-200"
                           x-bind:class="activeDropdown === 'campaigns' ? 'rotate-180' : ''" />
              </div>
            </a>

            {{-- Подменю Campaigns --}}
            <div class="absolute top-full left-0 w-48 bg-white text-black shadow-lg rounded-md overflow-hidden transition-all duration-200 transform origin-top"
                 x-show="activeDropdown === 'campaigns'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 x-cloak>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Active</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Archived</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150">Templates</a>
            </div>
          </div>

          {{-- Sales --}}
          <div class="menu-item relative"
               @mouseenter="activeDropdown = 'sales'"
               @mouseleave="activeDropdown = null">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200 hover:text-gray-300">
              Sales
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <x-svg-icon name="angle-bottom" class="transition-transform duration-200"
                           x-bind:class="activeDropdown === 'sales' ? 'rotate-180' : ''" />
              </div>
            </a>

            {{-- Подменю Sales --}}
            <div class="absolute top-full left-0 w-48 bg-white text-black shadow-lg rounded-md overflow-hidden transition-all duration-200 transform origin-top"
                 x-show="activeDropdown === 'sales'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 x-cloak>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Reports</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Analytics</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150">Dashboard</a>
            </div>
          </div>

          {{-- Sponsoring --}}
          <div class="menu-item relative"
               @mouseenter="activeDropdown = 'sponsoring'"
               @mouseleave="activeDropdown = null">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200 hover:text-gray-300">
              Sponsoring
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <x-svg-icon name="angle-bottom" class="transition-transform duration-200"
                           x-bind:class="activeDropdown === 'sponsoring' ? 'rotate-180' : ''" />
              </div>
            </a>

            {{-- Подменю Sponsoring --}}
            <div class="absolute top-full left-0 w-48 bg-white text-black shadow-lg rounded-md overflow-hidden transition-all duration-200 transform origin-top"
                 x-show="activeDropdown === 'sponsoring'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 x-cloak>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Events</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150 border-b border-gray-100">Programs</a>
              <a href="#" class="!no-underline block px-4 py-3 text-sm hover:bg-gray-100 transition-colors duration-150">Partnerships</a>
            </div>
          </div>

        </div>
      </nav>

      {{-- Правая часть с иконками --}}
      <div class="hidden md:block">
        <div class="ml-4 flex items-center space-x-1">

          {{-- Поиск --}}
          <a href="#" class="!no-underline flex items-center justify-center text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200">
            <x-svg-icon name="search" class="size-6" />
          </a>

          {{-- Экспорт --}}
          <a href="#" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200">
            <x-svg-icon name="upload" class="size-6" />
          </a>

          {{-- Корзина --}}
          @if(class_exists('WooCommerce'))
            <div x-data="cartCounter" x-init="count = {{ WC()->cart->get_cart_contents_count() }}">
              <a href="{{ wc_get_cart_url() }}" class="block text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200 relative">
                <x-svg-icon name="cart" class="size-6" />
                <span x-show="count > 0"
                      x-text="count"
                      x-transition
                      class="absolute -top-1 -right-1 bg-red-500 text-xs w-5 h-5 rounded-full flex items-center justify-center">
                </span>
              </a>
            </div>
          @else
            <a href="#" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200 relative">
              <x-svg-icon name="cart" class="size-6" />
              <span class="absolute -top-1 -right-1 bg-red-500 text-xs w-5 h-5 rounded-full flex items-center justify-center">3</span>
            </a>
          @endif

          {{-- Аккаунт --}}
          <a href="{{ wp_login_url() }}" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200" aria-label="Войти">
            <x-svg-icon name="user" class="" />
          </a>

        </div>
      </div>

      {{-- Мобильное меню кнопка --}}
      <div class="md:hidden" x-data="{ mobileMenuOpen: false }">
        <button class="text-white hover:text-gray-300 p-2 rounded-md"
                @@click="mobileMenuOpen = !mobileMenuOpen"
                aria-label="Меню">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>

        {{-- Мобильное меню --}}
        <div class="absolute top-full left-0 right-0 bg-gray-900 shadow-lg transition-all duration-300 transform origin-top"
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
             @@click.outside="mobileMenuOpen = false"
             x-cloak>
          <div class="px-4 pt-2 pb-3 space-y-1">
            <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Products</a>
            <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Channels</a>
            <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Campaigns</a>
            <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Sales</a>
            <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Sponsoring</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</header>

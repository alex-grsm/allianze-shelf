<header class="header bg-black text-white xl:min-h-20 flex items-center absolute top-0 left-0 right-0 z-50">
  <div class="container">
    <div class="flex items-center justify-between h-16">

      {{-- Логотип --}}
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <a href="{{ home_url('/') }}" class="flex items-center">
            <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="{{ get_bloginfo('name') }}">
          </a>
        </div>
      </div>

      {{-- Главное меню --}}
      @include('components.main-menu')

      {{-- Правая часть с иконками --}}
      <div class="hidden md:block">
        <div class="ml-4 flex items-center space-x-1">

          {{-- Поиск --}}
          @include('components.header-search')

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

      {{-- Мобильное меню --}}
      @include('components.mobile-menu')

    </div>
  </div>
</header>

<header class="bg-black text-white xl:min-h-20 flex items-center fixed top-0 left-0 right-0 z-50">
  <div class="container ">
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
      <nav class="hidden md:block">
        <div class="flex items-baseline space-x-7">

          {{-- Products --}}
          <div class="menu-item relative">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200">
              Products
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0.704102 0.75L5.2041 5.25L9.7041 0.75" stroke="white" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </a>
          </div>

          {{-- Channels --}}
          <div class="menu-item relative">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200">
              Channels
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0.704102 0.75L5.2041 5.25L9.7041 0.75" stroke="white" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </a>
          </div>

          {{-- Campaigns --}}
          <div class="menu-item relative">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200">
              Campaigns
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0.704102 0.75L5.2041 5.25L9.7041 0.75" stroke="white" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </a>
          </div>

          {{-- Sales --}}
          <div class="menu-item relative">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200">
              Sales
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0.704102 0.75L5.2041 5.25L9.7041 0.75" stroke="white" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </a>
          </div>

          {{-- Sponsoring --}}
          <div class="menu-item relative">
            <a href="#" class="!no-underline text-white px-3 py-2 flex items-center transition-colors duration-200">
              Sponsoring
              <div class="ml-0.5 size-4 flex items-center justify-center">
                <svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0.704102 0.75L5.2041 5.25L9.7041 0.75" stroke="white" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </a>
          </div>

        </div>
      </nav>

      {{-- Правая часть с иконками --}}
      <div class="hidden md:block">
        <div class="ml-4 flex items-center space-x-2">

          {{-- Поиск --}}
          <a href="#" class="!no-underline flex items-center justify-center text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200" aria-label="Поиск">
            <i class="fa-solid fa-search text-xl"></i>
          </a>

          {{-- Экспорт --}}
          <a href="#" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200">
            <i class="fa-solid fa-arrow-up-from-bracket text-xl"></i>
          </a>

          {{-- Корзина --}}
          @if(class_exists('WooCommerce'))
            <a href="{{ wc_get_cart_url() }}" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200 relative" aria-label="Корзина">
                <i class="fa-solid fa-cart-shopping text-xl"></i>
              @if(WC()->cart && WC()->cart->get_cart_contents_count() > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-xs w-5 h-5 rounded-full flex items-center justify-center">
                  {{ WC()->cart->get_cart_contents_count() }}
                </span>
              @endif
            </a>
          @else
            <a href="#" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200 relative" aria-label="Корзина">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
              </svg>
              <span class="absolute -top-1 -right-1 bg-red-500 text-xs w-5 h-5 rounded-full flex items-center justify-center">3</span>
            </a>
          @endif

          {{-- Аккаунт --}}
          {{-- @auth --}}
            {{-- <a href="{{ get_edit_user_link() }}" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200" aria-label="Профиль">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </a> --}}
          {{-- @else --}}
            <a href="{{ wp_login_url() }}" class="text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200" aria-label="Войти">
              <i class="fa-regular fa-user text-xl"></i>
            </a>
          {{-- @endauth --}}

        </div>
      </div>

      {{-- Мобильное меню кнопка --}}
      <div class="md:hidden">
        <button class="text-white hover:text-gray-300 p-2 rounded-md" id="mobile-menu-button" aria-label="Меню">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>

    </div>
  </div>

  {{-- Мобильное меню --}}
  <div class="md:hidden hidden absolute top-14 inset-x-0 p-2 transition transform origin-top-right z-100" id="mobile-menu">
    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-900">
      <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Products</a>
      <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Channels</a>
      <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Campaigns</a>
      <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Sales</a>
      <a href="#" class="text-white hover:text-gray-300 block px-3 py-2 text-sm font-medium">Sponsoring</a>
    </div>
  </div>
</header>

{{-- Простой JavaScript для мобильного меню --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  const mobileMenu = document.getElementById('mobile-menu');

  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', function() {
      mobileMenu.classList.toggle('hidden');
    });
  }
});
</script>

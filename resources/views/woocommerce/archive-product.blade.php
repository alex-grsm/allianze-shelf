{{--
  Template for product category pages
--}}

{{-- @extends('layouts.app')

@section('content')
    @include('sections.hero')
    @include('sections.car')
    @include('sections.cat-1')
    @include('sections.cat-2')
    @include('sections.related')
@endsection --}}

@extends('layouts.app')

@section('content')
  <div class="archive-products">

    {{-- Заголовок архива --}}
    <header class="archive-header mb-8">
      <h1 class="text-3xl font-bold mb-4">
        @if(is_shop())
          {{ woocommerce_page_title(false) }}
        @elseif(is_product_category())
          {{ single_cat_title('', false) }}
        @elseif(is_product_tag())
          {{ single_tag_title('', false) }}
        @else
          Товары
        @endif
      </h1>

      {{-- Описание категории/архива --}}
      @if(is_product_category() && category_description())
        <div class="archive-description text-gray-600 mb-4">
          {!! category_description() !!}
        </div>
      @endif
    </header>

    {{-- WooCommerce уведомления --}}
    @php(wc_print_notices())

    {{-- Хуки перед списком товаров --}}
    @php(do_action('woocommerce_before_shop_loop'))

    @if(woocommerce_product_loop())

      {{-- Сортировка и отображение результатов --}}
      <div class="shop-controls flex justify-between items-center mb-6 flex-wrap gap-4">
        <div class="result-count">
          @php(woocommerce_result_count())
        </div>
        <div class="ordering">
          @php(woocommerce_catalog_ordering())
        </div>
      </div>

      {{-- Начало цикла товаров --}}
      @php(woocommerce_product_loop_start())

      <div class="products-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @while(have_posts())
          @php(the_post())

          {{-- Подключение нашей кастомной карточки товара --}}
          <div class="product-wrapper">
            @include('partials.product-card')
          </div>

        @endwhile
      </div>

      {{-- Конец цикла товаров --}}
      @php(woocommerce_product_loop_end())

      {{-- Пагинация --}}
      <div class="pagination-wrapper mt-8">
        @php(woocommerce_pagination())
      </div>

    @else
      {{-- Товары не найдены --}}
      <div class="no-products-found">
        @php(do_action('woocommerce_no_products_found'))

        <div class="text-center py-12">
          <h2 class="text-xl font-semibold mb-4">Товары не найдены</h2>
          <p class="text-gray-600 mb-6">К сожалению, товары по вашему запросу не найдены.</p>
          <a href="{{ wc_get_page_permalink('shop') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition-colors">
            Вернуться в магазин
          </a>
        </div>
      </div>
    @endif

    {{-- Хуки после списка товаров --}}
    @php(do_action('woocommerce_after_shop_loop'))

  </div>
@endsection

{{-- Подключение стилей для WooCommerce (опционально) --}}
@push('styles')
  <style>
    /* Дополнительные стили для архива товаров */
    .products-grid {
      margin-bottom: 2rem;
    }

    .product-wrapper {
      height: 100%;
    }

    .product-card {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .product-info {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .add-to-cart {
      margin-top: auto;
    }

    /* Стили для отладочной секции */
    .debug-section {
      max-height: 300px;
      overflow-y: auto;
      font-size: 11px;
    }

    .debug-section pre {
      white-space: pre-wrap;
      word-break: break-all;
    }
  </style>
@endpush

{{-- Подключение JavaScript для AJAX корзины (опционально) --}}
@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // AJAX добавление в корзину
      const addToCartButtons = document.querySelectorAll('[data-ajax-add-to-cart="true"]');

      addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();

          const productId = this.getAttribute('data-product-id');

          // Здесь можно добавить AJAX запрос для добавления товара в корзину
          console.log('Добавление товара в корзину:', productId);

          // Временно перенаправляем на обычную ссылку
          window.location.href = this.getAttribute('href');
        });
      });
    });
  </script>
@endpush

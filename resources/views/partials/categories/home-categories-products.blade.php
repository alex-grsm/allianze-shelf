{{-- resources/views/partials/categories/home-categories-products.blade.php --}}

@php
  // Получаем главные категории WooCommerce (родительские категории)
  $main_categories = get_terms([
      'taxonomy'   => 'product_cat',
      'parent'     => 0, // только родительские категории
      'hide_empty' => true,
      'number'     => 6, // ограничиваем количество категорий (по желанию)
      'orderby'    => 'menu_order', // или 'name', 'count' в зависимости от ваших потребностей
      'order'      => 'ASC',
  ]);
@endphp

@if (!empty($main_categories) && !is_wp_error($main_categories))
  <section class="home-categories-section">

      @foreach ($main_categories as $main_category)
        <div class="main-category-block">

          @php
            $products = new WP_Query([
              'post_type'      => 'product',
              'posts_per_page' => 12,
              'tax_query'      => [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $main_category->term_id,
              ]],
            ]);
          @endphp

          @if ($products->have_posts())
            <div class="main-category-block__content py-12.5 relative">
                <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+10px)]">
                    {{-- Заголовок секции --}}
                    <div class="mb-6 max-w-5xl">
                        <h2 class="main-category-block__title text-3xl lg:text-4xl">
                            {{ $main_category->name }}
                        </h2>
                    </div>

                    <div class="crop-cards-slider ">
                        <div class="swiper-wrapper">
                            @while ($products->have_posts())
                              @php($products->the_post())
                              <div class="swiper-slide">
                                @include('partials.product-card')
                              </div>
                            @endwhile
                            @php(wp_reset_postdata())
                        </div>
                    </div>
                </div>
            </div>
          @endif

        </div>
      @endforeach

  </section>
@endif

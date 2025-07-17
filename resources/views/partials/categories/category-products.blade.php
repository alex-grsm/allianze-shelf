{{-- resources/views/partials/categories/category-products.blade.php --}}

@php
  $current_category = get_queried_object(); // WP_Term
  $child_categories = get_terms([
      'taxonomy'   => 'product_cat',
      'parent'     => $current_category->term_id,
      'hide_empty' => true,
]);
@endphp

@if (!empty($child_categories) && !is_wp_error($child_categories))
  <section class="subcategories-section">

      @foreach ($child_categories as $subcategory)
        <div class="subcategory-block">

          @php
            $products = new WP_Query([
              'post_type'      => 'product',
              'posts_per_page' => 12,
              'tax_query'      => [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $subcategory->term_id,
              ]],
            ]);
          @endphp

          @if ($products->have_posts())
            <div class="subcategory-block__content py-12.5 relative">
                <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+10px)]">
                    {{-- Заголовок секции --}}
                    <div class="mb-6 max-w-5xl">
                        {{-- <h2 class="text-3xl text-white lg:text-4xl"> --}}
                        <h2 class="subcategory-block__title text-3xl lg:text-4xl">
                            {{ $subcategory->name }}
                        </h2>

                        <div class="mt-4 ej-filter">
                            <button type="button"
                                class="ej-filter__button inline-flex items-center gap-2 px-3 py-1 neutral-100 border-2 border-black rounded-4xl text-black text-xl">
                                <x-svg-icon name="filter" class="ej-filter__icon [&_path]:stroke-black" />
                                <span>Filter</span>
                            </button>
                        </div>
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

          {{-- @if ($products->have_posts())
            <div class="products-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
              @while ($products->have_posts())
                @php($products->the_post())
                <div class="product-wrapper">
                  @include('partials.product-card')
                </div>
              @endwhile
              @php(wp_reset_postdata())
            </div>
          @else
            <p class="text-gray-500">There are no products in this category.</p>
          @endif --}}

        </div>
      @endforeach

  </section>
@endif


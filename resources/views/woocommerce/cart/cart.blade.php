{{--
  Cart Page - Simple Clean Design
  @see https://woocommerce.com/document/template-structure/
  @package WooCommerce\Templates
  @version 7.9.0
--}}

@php
  defined('ABSPATH') || exit;
@endphp

<div class="container">
  @php do_action('woocommerce_before_cart'); @endphp

  <form class="woocommerce-cart-form py-10" action="{{ esc_url(wc_get_cart_url()) }}" method="post">
    @php do_action('woocommerce_before_cart_table'); @endphp

    <!-- Cart Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <!-- Table Header -->
      <div class="bg-gray-50 border-b border-gray-200">
        <div class="grid grid-cols-12 gap-4 px-6 py-4">
          <div class="col-span-10">
            <span class="text-sm font-medium text-gray-700 uppercase tracking-wider">{{ __('Product', 'woocommerce') }}</span>
          </div>
          <div class="col-span-2 text-right">
            <span class="text-sm font-medium text-gray-700 uppercase tracking-wider">{{ __('Price', 'woocommerce') }}</span>
          </div>
        </div>
      </div>

      <!-- Cart Items -->
      <div class="divide-y divide-gray-200">
        @php do_action('woocommerce_before_cart_contents'); @endphp

        @foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item)
          @php
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
          @endphp

          @if($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key))
            @php
              $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            @endphp

            <div class="grid grid-cols-12 gap-4 px-6 py-6 items-center hover:bg-gray-50 transition-colors">

              <!-- Product Info -->
              <div class="col-span-10">
                <div class="flex items-start space-x-4">
                  <!-- Product Image -->
                  <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                    @php
                      $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail', ['class' => 'w-full !h-full object-cover']), $cart_item, $cart_item_key);
                    @endphp

                    @if(!$product_permalink)
                      {!! $thumbnail !!}
                    @else
                      <a href="{{ esc_url($product_permalink) }}" class="block w-full h-full">
                        {!! $thumbnail !!}
                      </a>
                    @endif
                  </div>

                  <!-- Product Details -->
                  <div class="flex-1 min-w-0">
                    <!-- Content Type & Product Category -->
                    <div class="flex space-x-4 text-xs text-gray-500 mb-1">
                      <div>
                        <span class="text-gray-400">Content type</span><br>
                        <span class="text-gray-600">{{ $_product->get_attribute('content-type') ?: 'Brand campaign' }}</span>
                      </div>
                      <div>
                        <span class="text-gray-400">Category</span><br>
                        @php
                          $product_cats = get_the_terms($product_id, 'product_cat');
                          $cat_name = !empty($product_cats) ? $product_cats[0]->name : 'Uncategorized';
                        @endphp
                        <span class="text-gray-600">{{ $_product->get_attribute('product-category') ?: $cat_name }}</span>
                      </div>
                    </div>

                    <!-- Product Name -->
                    <div class="text-base font-medium text-blue-600 mb-2">
                      @if(!$product_permalink)
                        {{ $product_name }}
                      @else
                        <a href="{{ esc_url($product_permalink) }}" class="!no-underline hover:text-blue-700 transition-colors">
                          {{ $product_name }}
                        </a>
                      @endif
                    </div>

                    <!-- Product Attributes/Details -->
                    @php
                      $item_data = wc_get_formatted_cart_item_data($cart_item);
                      if ($item_data) {
                        echo '<div class="text-xs text-gray-500 mb-2">' . $item_data . '</div>';
                      }

                      // Custom attributes display
                      $attributes = $_product->get_attributes();
                      if (!empty($attributes)) {
                        echo '<div class="text-xs text-gray-600">';
                        if (isset($attributes['assets-count'])) {
                          $asset_terms = $attributes['assets-count']->get_terms();
                          if (!empty($asset_terms)) {
                            echo '<div>• ' . $asset_terms[0]->name . ' Assets</div>';
                          }
                        }
                        if (isset($attributes['resolution'])) {
                          $res_terms = $attributes['resolution']->get_terms();
                          if (!empty($res_terms)) {
                            echo '<div>• ' . $res_terms[0]->name . '</div>';
                          }
                        }
                        echo '</div>';
                      }
                    @endphp

                    @if($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity']))
                      <div class="text-xs text-amber-600 mt-1">
                        {{ __('Available on backorder', 'woocommerce') }}
                      </div>
                    @endif

                    <!-- Remove Link -->
                      @php
                        echo apply_filters(
                          'woocommerce_cart_item_remove_link',
                          sprintf(
                            '<a href="%s" class=" text-red-500 text-xs hover:text-red-700" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                            esc_attr__( 'Remove this item', 'woocommerce' ),
                            esc_attr( $product_id ),
                            esc_attr( $_product->get_sku() ),
                            __('Remove', 'woocommerce')
                          ),
                          $cart_item_key
                        );
                      @endphp
                  </div>
                </div>
              </div>

              <!-- Price -->
              <div class="col-span-2 text-right">
                <div class="text-xl font-semibold text-gray-900">
                  @php
                    echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                  @endphp
                </div>
                @if($cart_item['quantity'] > 1)
                  <div class="text-sm text-gray-500">
                    @php
                      echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                    @endphp
                    {{ __('each', 'woocommerce') }}
                  </div>
                @endif
              </div>
            </div>
          @endif
        @endforeach

        @php do_action('woocommerce_cart_contents'); @endphp
      </div>
    </div>

    @php do_action('woocommerce_after_cart_table'); @endphp
    @php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); @endphp
  </form>

  @php do_action('woocommerce_before_cart_collaterals'); @endphp

  <!-- Cart Summary & Actions -->
  <div class="mt-8 flex flex-col lg:flex-row gap-8 justify-between items-start  pb-15">
    <!-- Continue Shopping -->
    <div>
      <a href="{{ esc_url(wc_get_page_permalink('shop')) }}"
         class="capitalize inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
        {{ __('Back to shopping', 'woocommerce') }}
      </a>
    </div>

    <!-- Cart Totals & Buy Button -->
    <div class="flex flex-col items-end">
      <!-- Buy Button -->
      <a href="{{ esc_url(wc_get_checkout_url()) }}"
         class="!no-underline inline-flex items-center justify-center px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors min-w-[120px]">
        {{ __('Buy', 'woocommerce') }}
      </a>
    </div>
  </div>

  @php do_action('woocommerce_after_cart'); @endphp
</div>

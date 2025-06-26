{{--
  Empty cart page
  @see https://woocommerce.com/document/template-structure/
  @package WooCommerce\Templates
  @version 7.0.1
--}}

@php
  defined('ABSPATH') || exit;
@endphp

@extends('layouts.app')

@section('content')
  @include('sections.hero-cart')

  <div class="container py-10">
    @php
      /*
       * @hooked wc_empty_cart_message - 10
       */
      do_action('woocommerce_cart_is_empty');
    @endphp

    @if(wc_get_page_id('shop') > 0)
      <p class="return-to-shop">
        <a class="button wc-backward{{ wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '' }}"
           href="{{ esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))) }}">
          {{ esc_html(apply_filters('woocommerce_return_to_shop_text', __('Return to shop', 'woocommerce'))) }}
        </a>
      </p>
    @endif
  </div>
@endsection

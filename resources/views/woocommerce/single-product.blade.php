{{-- resources/views/woocommerce/single-product.blade.php --}}
{{--
The Template for displaying all single products
@package WooCommerce\Templates
@version 1.6.4
--}}

@extends('layouts.app')

@section('content')
    @include('partials.single-product.product-summary')
    @include('partials.single-product.asset-overview')
    @include('partials.single-product.asset-overview-list')
    @include('partials.single-product.product-channels')
    @include('partials.single-product.buyout-details')
    @include('partials.single-product.product-links')
    @include('partials.single-product.attachments')
@endsection

{{-- resources/views/woocommerce/single-product.blade.php --}}
{{--
The Template for displaying all single products
@package WooCommerce\Templates
@version 1.6.4
--}}

@extends('layouts.app')

@section('content')

@php
    $productId = get_the_ID();
    $tags = get_product_tags_display($productId, 25);
    $cardTags = get_product_card_tags($productId, 25);
    $relatedProducts = get_related_products_by_tags($productId, 25);
@endphp

<div style="background: #f0f0f0; padding: 20px; margin: 70px 0; border-radius: 8px;">
    <h4>🏷️ Tags Testing Debug</h4>

    <div style="margin-bottom: 15px;">
        <strong>Product ID:</strong> {{ $productId }}<br>
        <strong>Product Name:</strong> {{ get_the_title() }}
    </div>

    <p><strong>Tags as text:</strong> {{ $tags ?: 'No tags' }}</p>
    <p><strong>Card tags count:</strong> {{ count($cardTags) }}</p>
    <p><strong>Related products:</strong> {{ count($relatedProducts) }}</p>

    @if(!empty($cardTags))
        <div style="margin: 15px 0;">
            <strong>Card tags:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @foreach($cardTags as $tag)
                    <li>
                        <strong>{{ $tag['name'] }}</strong>
                        (Level: {{ $tag['level'] }},
                        Parent: {{ $tag['parent'] ?: 'none' }})
                        @if($tag['url'])
                            - <a href="{{ $tag['url'] }}" target="_blank">View</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($relatedProducts))
        <div style="margin: 15px 0;">
            <strong>Related products:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @foreach($relatedProducts as $product)
                    <li>
                        <a href="{{ $product->get_permalink() }}" target="_blank">
                            {{ $product->get_name() }}
                        </a>
                        (ID: {{ $product->get_id() }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Дополнительные тесты функций
        $allProductTags = \App\Taxonomies\ProductTagsHierarchy::getProductTags($productId);
        $mainCategories = get_product_tag_categories();

        // Тест проверки конкретного тега
        $hasSpecificTag = false;
        if (!empty($cardTags)) {
            $firstTagSlug = $cardTags[0]['slug'];
            $hasSpecificTag = product_has_tag($productId, $firstTagSlug);
        }
    @endphp

    <div style="border-top: 1px solid #ccc; padding-top: 15px; margin-top: 15px;">
        <strong>Additional Debug Info:</strong><br>
        <small>
            • Total tags from hierarchy: {{ count($allProductTags) }}<br>
            • Main categories available: {{ count($mainCategories) }}<br>
            @if(!empty($cardTags))
                • Has tag "{{ $cardTags[0]['slug'] }}": {{ $hasSpecificTag ? 'Yes' : 'No' }}<br>
            @endif
        </small>
    </div>

    @if(!empty($mainCategories))
        <details style="margin-top: 15px;">
            <summary style="cursor: pointer; font-weight: bold;">📋 Available Tag Categories</summary>
            <ul style="margin: 10px 0; padding-left: 20px;">
                @foreach($mainCategories as $category)
                    <li>
                        <strong>{{ $category['name'] }}</strong>
                        ({{ $category['count'] }} products, {{ count($category['children']) }} subcategories)
                    </li>
                @endforeach
            </ul>
        </details>
    @endif

    <details style="margin-top: 10px;">
        <summary style="cursor: pointer; font-weight: bold;">🔍 Full Raw Data</summary>
        <div style="background: white; padding: 10px; margin-top: 10px; border-radius: 4px;">
            <strong>All Product Tags:</strong>
            <pre style="font-size: 11px; overflow: auto; max-height: 200px;">{{ json_encode($allProductTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

            <strong>Card Tags:</strong>
            <pre style="font-size: 11px; overflow: auto; max-height: 150px;">{{ json_encode($cardTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </details>
</div>



    @include('partials.single-product.product-summary')
    @include('partials.single-product.asset-overview')
    @include('partials.single-product.asset-overview-list')
    @include('partials.single-product.product-channels')
    @include('partials.single-product.buyout-details')
    @include('partials.single-product.product-links')
    @include('partials.single-product.attachments')
@endsection

@php
    $products = [
        [
            'image' => 'resources/images/demo/prod-11.webp',
            'label' => 'Easy adaptable',
            'flag' => 'DE',
            'title' => 'AI Home Campaign',
            'target' => 'homeowners, renters, families',
            'year' => '2023',
            'buyout' => '5.000€',
            'tag' => 'Home',
        ],
        [
            'image' => 'resources/images/demo/prod-12.webp',
            'label' => 'Featured',
            'flag' => 'AT',
            'title' => 'Vorsorge Kampagne',
            'target' => 'young adults, career starters',
            'year' => '2023',
            'buyout' => '45.000€',
            'tag' => 'Life',
        ],
        [
            'image' => 'resources/images/demo/prod-13.webp',
            'label' => null,
            'flag' => 'CZ',
            'title' => 'Solar Home Product',
            'target' => 'families, business partner',
            'year' => '2025',
            'buyout' => '2.000€',
            'tag' => 'Smart Home',
        ],
        [
            'image' => 'resources/images/demo/prod-14.webp',
            'label' => null,
            'flag' => 'ID',
            'title' => 'Legacy Planning',
            'target' => 'customer, individuals',
            'year' => '2025',
            'buyout' => '1.900€',
            'tag' => 'Liability',
        ],
        [
            'image' => 'resources/images/demo/prod-15.webp',
            'label' => null,
            'flag' => 'ES',
            'title' => 'Life Insurance Assets',
            'target' => 'families, individuals',
            'year' => '2023',
            'buyout' => '2.100€',
            'tag' => 'Life Insurance',
        ],
        [
            'image' => 'resources/images/demo/prod-15.webp',
            'label' => null,
            'flag' => 'ES',
            'title' => 'Life Insurance Assets',
            'target' => 'families, individuals',
            'year' => '2023',
            'buyout' => '2.100€',
            'tag' => 'Life Insurance',
        ],
        [
            'image' => 'resources/images/demo/prod-15.webp',
            'label' => null,
            'flag' => 'ES',
            'title' => 'Life Insurance Assets',
            'target' => 'families, individuals',
            'year' => '2023',
            'buyout' => '2.100€',
            'tag' => 'Life Insurance',
        ],
        [
            'image' => 'resources/images/demo/prod-15.webp',
            'label' => null,
            'flag' => 'ES',
            'title' => 'Life Insurance Assets',
            'target' => 'families, individuals',
            'year' => '2023',
            'buyout' => '2.100€',
            'tag' => 'Life Insurance',
        ],
    ];
@endphp
<section class="py-12.5 relative">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+1rem)]">
        {{-- Заголовок секции --}}
        <div class="mb-6">
            <h2 class="text-3xl lg:text-4xl">
                Products
            </h2>
        </div>

        <div class="crop-cards-slider ">
            <div class="swiper-wrapper">
                @foreach ($products as $product)
                    <div class="swiper-slide">
                        @include('components.card', $product)
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

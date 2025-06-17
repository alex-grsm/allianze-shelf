@php
    $channels = [
        [
            'image' => 'resources/images/demo/prod-6.webp',
            'label' => 'Featured',
            'flag' => 'GB',
            'title' => 'Support for supporters',
            'target' => 'fans, athletes, community',
            'year' => '2023',
            'buyout' => '16*',
            'tag' => 'Webpage',
        ],
        [
            'image' => 'resources/images/demo/prod-7.webp',
            'label' => 'Featured',
            'flag' => 'CN',
            'title' => 'Allianz Vista',
            'target' => 'Brand awareness',
            'year' => '2024',
            'buyout' => '4.000€*',
            'tag' => 'Posters',
        ],
        [
            'image' => 'resources/images/demo/prod-8.webp',
            'label' => 'Featured',
            'flag' => 'CH',
            'title' => 'Advisor for new drivers',
            'target' => 'drivers, families, commuters',
            'year' => '2023',
            'buyout' => '16*',
            'tag' => 'Webpage',
        ],
        [
            'image' => 'resources/images/demo/prod-9.webp',
            'label' => null,
            'flag' => 'ID',
            'title' => '#GenerAZiFlexible',
            'target' => 'family and work',
            'year' => '2023',
            'buyout' => '800€',
            'tag' => 'Social Media',
        ],
        [
            'image' => 'resources/images/demo/prod-10.webp',
            'label' => null,
            'flag' => 'SK',
            'title' => 'NatCat communication',
            'target' => 'Homeowners, flood zones',
            'year' => '2023',
            'buyout' => '800€',
            'tag' => 'Social Media',
        ],
        [
            'image' => 'resources/images/demo/prod-10.webp',
            'label' => null,
            'flag' => 'SK',
            'title' => 'NatCat communication',
            'target' => 'Homeowners, flood zones',
            'year' => '2023',
            'buyout' => '800€',
            'tag' => 'Social Media',
        ],
        [
            'image' => 'resources/images/demo/prod-10.webp',
            'label' => null,
            'flag' => 'SK',
            'title' => 'NatCat communication',
            'target' => 'Homeowners, flood zones',
            'year' => '2023',
            'buyout' => '800€',
            'tag' => 'Social Media',
        ],
    ];
@endphp
<section class="py-12.5 relative">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+1rem)]">
        {{-- Заголовок секции --}}
        <div class="mb-6">
            <h2 class="text-3xl lg:text-4xl">
                Channels
            </h2>
        </div>

        <div class="crop-cards-slider ">
            <div class="swiper-wrapper">
                @foreach ($channels as $chanel)
                    <div class="swiper-slide">
                        @include('components.card', $chanel)
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@php
    $campaigns = [
        [
            'image' => 'resources/images/demo/prod-1.webp',
            'label' => 'Featured',
            'flag' => 'DE',
            'title' => 'Always drive well',
            'target' => 'drivers, families, commuters',
            'year' => '2023',
            'buyout' => '45.000€*',
            'tag' => 'Car Insurance',
        ],
        [
            'image' => 'resources/images/demo/prod-2.webp',
            'label' => 'Easy adaptable',
            'flag' => 'CN',
            'title' => 'The Strength to Move Forward',
            'target' => 'fans, young adults & families',
            'year' => '2023',
            'buyout' => '5.000€*',
            'tag' => 'OPM Partnership',
        ],
        [
            'image' => 'resources/images/demo/prod-3.webp',
            'label' => null,
            'flag' => 'CH',
            'title' => 'The Great Goodbye',
            'target' => 'life, pension',
            'year' => '2023',
            'buyout' => '15.000€*',
            'tag' => 'Retirement',
        ],
        [
            'image' => 'resources/images/demo/prod-4.webp',
            'label' => null,
            'flag' => null,
            'title' => 'MoveNow',
            'target' => 'young & sporty people',
            'year' => '2023',
            'buyout' => '1€',
            'tag' => 'OPM Partnership',
        ],
        [
            'image' => 'resources/images/demo/prod-5.webp',
            'label' => null,
            'flag' => 'AT',
            'title' => 'Ready for Life',
            'target' => 'young & sporty people',
            'year' => '2023',
            'buyout' => '1€',
            'tag' => 'OPM Partnership',
        ],
        [
            'image' => 'resources/images/demo/prod-5.webp',
            'label' => null,
            'flag' => 'AT',
            'title' => 'Ready for Life',
            'target' => 'young & sporty people',
            'year' => '2023',
            'buyout' => '1€',
            'tag' => 'OPM Partnership',
        ],
        [
            'image' => 'resources/images/demo/prod-5.webp',
            'label' => null,
            'flag' => 'AT',
            'title' => 'Ready for Life',
            'target' => 'young & sporty people',
            'year' => '2023',
            'buyout' => '1€',
            'tag' => 'OPM Partnership',
        ],
    ];
@endphp
<section class="pb-12.5 -mt-103.5 z-20 relative">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+10px)]">
        {{-- Заголовок секции --}}
        <div class="mb-6">
            <h2 class="text-3xl text-white lg:text-4xl">
                Campaigns
            </h2>
        </div>


        {{-- <div class="flex  gap-6">
            @foreach ($campaigns as $campaign)
                @include('components.card', $campaign)
            @endforeach
        </div> --}}
        <div class="crop-cards-slider ">
            <div class="swiper-wrapper">
                @foreach ($campaigns as $campaign)
                    <div class="swiper-slide">
                        @include('components.card', $campaign)
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

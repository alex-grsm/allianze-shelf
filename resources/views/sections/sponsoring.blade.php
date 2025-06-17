@php
    $sponsorings = [
        [
            'image' => 'resources/images/demo/prod-21.webp',
            'label' => 'Featured',
            'flag' => 'AT',
            'title' => 'Get Ready For Paris',
            'target' => 'fans, athletes, community',
            'year' => '2023',
            'buyout' => '700€',
            'tag' => 'Gametime Activation',
        ],
        [
            'image' => 'resources/images/demo/prod-22.webp',
            'label' => null,
            'flag' => null,
            'title' => 'One Year Countdown Post',
            'target' => 'fans, athletes, community',
            'year' => '2023',
            'buyout' => '200€',
            'tag' => 'MiCo26 Countdown',
        ],
        [
            'image' => 'resources/images/demo/prod-23.webp',
            'label' => null,
            'flag' => 'BD',
            'title' => 'Olympic Athlete Partnership',
            'target' => 'customers, individuals',
            'year' => '2023',
            'buyout' => '1.000€',
            'tag' => 'Ambassador Comm',
        ],
        [
            'image' => 'resources/images/demo/prod-24.webp',
            'label' => null,
            'flag' => 'CN',
            'title' => 'Sponsorship Fudan University',
            'target' => 'finance, insurance sector',
            'year' => '2023',
            'buyout' => '1€',
            'tag' => 'Education Sponsoring',
        ],
        [
            'image' => 'resources/images/demo/prod-25.webp',
            'label' => null,
            'flag' => 'DE',
            'title' => 'Local Heroes Toolkit',
            'target' => 'fans, athletes, community',
            'year' => '2023',
            'buyout' => '100€',
            'tag' => 'Local Sport Sponsor',
        ],
    ];
@endphp
<section class="py-12.5 relative">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+1rem)]">
        {{-- Заголовок секции --}}
        <div class="mb-6">
            <h2 class="text-3xl lg:text-4xl">
                Sponsorings
            </h2>
        </div>

        <div class="crop-cards-slider ">
            <div class="swiper-wrapper">
                @foreach ($sponsorings as $sponsoring)
                    <div class="swiper-slide">
                        @include('components.card', $sponsoring)
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

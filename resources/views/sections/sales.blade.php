@php
  $sales = [
      [
          'image' => 'resources/images/demo/prod-16.webp',
          'label' => 'Featured',
          'flag' => 'DE',
          'title' => 'Allianz Vista for Agents',
          'target' => 'Brand awareness',
          'year' => '2024',
          'buyout' => '45.000€',
          'tag' => 'Agent Poster',
      ],
      [
          'image' => 'resources/images/demo/prod-17.webp',
          'label' => null,
          'flag' => 'AT',
          'title' => 'Digital Window',
          'target' => 'Event',
          'year' => '2023',
          'buyout' => '200€*',
          'tag' => 'PoS Branding',
      ],
      [
          'image' => 'resources/images/demo/prod-18.webp',
          'label' => null,
          'flag' => 'US',
          'title' => 'Agent Poster',
          'target' => 'Brand awareness',
          'year' => '2022',
          'buyout' => '350€*',
          'tag' => 'Sales Promotion',
      ],
      [
          'image' => 'resources/images/demo/prod-19.webp',
          'label' => null,
          'flag' => null,
          'title' => 'Vehicle Branding',
          'target' => 'Brand awareness',
          'year' => '2025',
          'buyout' => '1€*',
          'tag' => 'Vehicle Branding',
      ],
      [
          'image' => 'resources/images/demo/prod-20.webp',
          'label' => null,
          'flag' => 'AT',
          'title' => 'Agent Social Media Campaign',
          'target' => 'Branding',
          'year' => '2025',
          'buyout' => '300€*',
          'tag' => 'Digital Agent Ads',
      ],
  ];
@endphp
<section class="py-12.5 relative">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+10px)]">
        {{-- Заголовок секции --}}
        <div class="mb-6">
            <h2 class="text-3xl lg:text-4xl">
                Sales
            </h2>
        </div>

        <div class="crop-cards-slider ">
            <div class="swiper-wrapper">
                @foreach ($sales as $sale)
                    <div class="swiper-slide">
                        @include('components.card', $sale)
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

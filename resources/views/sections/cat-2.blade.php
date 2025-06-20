@php
    $products = [
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
    ];
@endphp
<section class="py-12.5 relative">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+10px)]">
        {{-- Заголовок секции --}}
        <div class="mb-6">
            <h2 class="text-3xl lg:text-4xl">
                Liability
            </h2>

            <div class="mt-4">
                <button type="button"
                    class="inline-flex items-center gap-2 px-3 py-1 neutral-100 border-2 border-black rounded-4xl text-black text-xl">
                    <x-svg-icon name="filter" class="[&_path]:stroke-black" />
                    <span>Filter</span>
                </button>
            </div>
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

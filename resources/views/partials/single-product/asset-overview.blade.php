{{-- resources/views/partials/single-product/asset-overview.blade.php --}}

@if($assetOverview && $assetOverview['has_assets'])
<section class="asset-overview-section pb-20 pt-10">
  <div class="container">
      {{-- Заголовок и описание --}}
      <div class="mb-12.5">
          <h2 class="text-3xl font-bold text-blue-600 mb-4">
              Asset Overview
          </h2>

          <div class="text-blue-600 text-lg max-w-4xl leading-relaxed">
              {{ $assetOverview['description'] }}
          </div>
      </div>
  </div>
  <div class="container-fluid">
    <div class="asset-overview">

        {{-- Asset Slider --}}
        <div class="asset-overview-slider swiper overflow-hidden">
            <div class="swiper-wrapper items-center">
                @foreach($assetOverview['assets'] as $asset)
                    <div class="swiper-slide">
                        <div class="asset-slide relative group">
                            {{-- Asset Image --}}
                            <div class="relative overflow-hidden rounded-2xl shadow-lg">
                                <img src="{{ $asset['image']['sizes']['large'] ?? $asset['image']['url'] }}"
                                     alt="{{ $asset['label'] }}"
                                     class="w-full object-cover transition-transform duration-500 group-hover:scale-105">

                                {{-- Overlay градиент для лучшей читаемости текста --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>

                            {{-- Asset Label --}}
                            <div class="absolute bottom-4 right-4">
                                <div class="bg-blue-600/60 backdrop-blur-sm text-white px-4 py-2 rounded-lg font-medium text-lg shadow-lg">
                                    {{ $asset['label'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
  </div>
</section>
@endif

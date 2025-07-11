{{-- resources/views/partials/single-product/asset-overview.blade.php --}}

@if (
    !empty($assetOverview['has_assets']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['companies', 'social_media_assets'])
)

    <section class="asset-overview-section pb-20 pt-10">
        <div class="container">
            {{-- Заголовок и описание --}}
            <div class="mb-12.5">
                <h2 class="text-3xl font-bold text-blue-600 mb-4">
                    Asset Overview
                </h2>
                {{-- Проверяем наличие описания перед выводом --}}
                @if (!empty($assetOverview['description']))
                    <div class="text-blue-600 text-lg max-w-4xl leading-relaxed">
                        {{ $assetOverview['description'] }}
                    </div>
                @endif
            </div>
        </div>
        <div class="container-fluid">
            <div class="asset-overview">
                {{-- Asset Slider --}}
                <div class="asset-overview-slider swiper overflow-hidden">
                    <div class="swiper-wrapper items-center">
                        {{-- Дополнительная проверка массива assets --}}
                        @if (!empty($assetOverview['assets']) && is_array($assetOverview['assets']))
                            @foreach ($assetOverview['assets'] as $asset)
                                {{-- Проверяем что asset - это массив и содержит необходимые данные --}}
                                @if (is_array($asset) && !empty($asset['image']))
                                    <div class="swiper-slide">
                                        <div class="asset-slide relative group">
                                            {{-- Asset Image --}}
                                            <div class="relative overflow-hidden rounded-2xl shadow-lg">
                                                <img src="{{ $asset['image']['sizes']['large'] ?? ($asset['image']['url'] ?? '') }}"
                                                    alt="{{ $asset['label'] ?? 'Asset image' }}"
                                                    class="w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                                {{-- Overlay градиент для лучшей читаемости текста --}}
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                </div>
                                            </div>
                                            {{-- Asset Label (только если есть) --}}
                                            @if (!empty($asset['label']))
                                                <div class="absolute bottom-4 right-4">
                                                    <div
                                                        class="bg-blue-600/60 backdrop-blur-sm text-white px-4 py-2 rounded-lg font-medium text-lg shadow-lg">
                                                        {{ $asset['label'] }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

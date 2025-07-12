{{-- resources/views/partials/single-product/asset-overview-list.blade.php --}}

@if (
    !empty($assetOverviewList['has_items']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['newsletter', 'landing_page'])
)

    <section class="asset-overview-list-section pb-20 pt-10">
        <div class="container">
            {{-- Заголовок и описание --}}
            <div class="mb-12.5">
                <h2 class="text-3xl font-bold text-blue-600 mb-4">
                    Asset Overview
                </h2>
                {{-- Проверяем наличие описания перед выводом --}}
                @if (!empty($assetOverviewList['description']))
                    <div class="text-blue-600 text-lg max-w-4xl leading-relaxed">
                        {{ $assetOverviewList['description'] }}
                    </div>
                @endif
            </div>

            {{-- LIST DISPLAY --}}
            @if (!empty($assetOverviewList['items']) && is_array($assetOverviewList['items']))
                <div class="asset-overview-list space-y-12">
                    @foreach ($assetOverviewList['items'] as $item)
                        @if (is_array($item) && !empty($item['description']) && !empty($item['image']))
                            <div class="asset-overview-item
                                        @if($item['index'] % 2 === 0) lg:flex-row @else lg:flex-row-reverse @endif
                                        flex flex-col lg:gap-16 gap-8 items-center">

                                {{-- Text Content --}}
                                <div class="lg:w-1/2 w-full">
                                    {{-- Title (если есть) --}}
                                    @if (!empty($item['title']))
                                        <h3 class="text-2xl font-bold text-blue-600 mb-4">
                                            {{ $item['title'] }}
                                        </h3>
                                    @endif

                                    {{-- Description --}}
                                    <div class="text-blue-600 text-lg leading-relaxed">
                                        {!! nl2br(e($item['description'])) !!}
                                    </div>
                                </div>

                                {{-- Image --}}
                                <div class="lg:w-1/2 w-full">
                                    <div class="relative overflow-hidden rounded-2xl shadow-lg group">
                                        <img src="{{ $item['image']['sizes']['large'] ?? ($item['image']['url'] ?? '') }}"
                                            alt="{{ $item['image']['alt'] ?? ($item['title'] ?? 'Asset overview image') }}"
                                            class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-105"
                                            loading="lazy">

                                        {{-- Hover overlay --}}
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                {{-- Fallback если нет элементов --}}
                <div class="text-center py-12 text-gray-500">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <p class="text-lg font-medium">No asset overview items available</p>
                        <p class="text-sm mt-1">Asset overview content will appear here when added</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif

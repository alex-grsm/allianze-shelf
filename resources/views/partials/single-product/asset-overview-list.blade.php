{{-- resources/views/partials/single-product/asset-overview-list.blade.php --}}

@if (
    !empty($assetOverviewList['has_items']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['social_media_assets', 'newsletter', 'landing_page'])
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
                        {!! nl2br(e($assetOverviewList['description'])) !!}
                    </div>
                @endif
            </div>

            {{-- LIST DISPLAY --}}
            @if (!empty($assetOverviewList['items']) && is_array($assetOverviewList['items']))
                <div class="asset-overview-list space-y-12">
                    @foreach ($assetOverviewList['items'] as $item)
                        @if (is_array($item) && !empty($item['description']))
                            <div class="asset-overview-item
                                        @if($item['index'] % 2 === 0) lg:flex-row-reverse @else lg:flex-row @endif
                                        flex flex-col lg:gap-16 gap-8 items-center">

                                {{-- Text Content --}}
                                <div class="lg:w-1/2 w-full">
                                    {{-- Description --}}
                                    <div class="text-blue-600 text-lg leading-relaxed">
                                        {!! nl2br(e($item['description'])) !!}
                                    </div>
                                </div>

                                {{-- Media Content --}}
                                <div class="lg:w-1/2 w-full">
                                    <div class="relative overflow-hidden rounded-2xl shadow-lg group">

                                        {{-- VIDEO DISPLAY --}}
                                        @if (!empty($item['media_type']) && $item['media_type'] === 'video' && !empty($item['video']))
                                            <div class="video-container">
                                                <video
                                                    class="w-full h-auto rounded-2xl"
                                                    @if (!empty($item['video_poster']))
                                                        poster="{{ $item['video_poster']['sizes']['large'] ?? ($item['video_poster']['url'] ?? '') }}"
                                                    @endif
                                                    {{-- Кастомные контролы - НЕ используем атрибут controls --}}
                                                    @if (!empty($item['video_settings']) && in_array('autoplay', $item['video_settings']) && in_array('muted', $item['video_settings']))
                                                        autoplay
                                                    @endif
                                                    @if (!empty($item['video_settings']) && in_array('loop', $item['video_settings']))
                                                        loop
                                                    @endif
                                                    @if (!empty($item['video_settings']) && in_array('muted', $item['video_settings']))
                                                        muted
                                                    @endif
                                                    preload="metadata"
                                                    playsinline
                                                    tabindex="0"
                                                    {{-- Данные для кастомных контролов --}}
                                                    data-video-index="{{ $item['index'] }}"
                                                    data-has-custom-controls="true"
                                                >
                                                    <source src="{{ $item['video']['url'] }}" type="{{ $item['video']['mime_type'] ?? 'video/mp4' }}">

                                                    {{-- Fallback для старых браузеров --}}
                                                    <p class="text-center text-gray-500 py-8">
                                                        Your browser does not support the video tag.
                                                        <a href="{{ $item['video']['url'] }}" class="text-blue-600 hover:text-blue-800 underline ml-2">
                                                            Download video
                                                        </a>
                                                    </p>
                                                </video>

                                                {{-- Video overlay for styling - будет удалено кастомными контролами --}}
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent opacity-0 group-hover:opacity-50 transition-opacity duration-300 pointer-events-none rounded-2xl">
                                                </div>
                                            </div>

                                        {{-- IMAGE DISPLAY --}}
                                        @elseif (!empty($item['image']))
                                            <img src="{{ $item['image']['sizes']['large'] ?? ($item['image']['url'] ?? '') }}"
                                                alt="{{ $item['image']['alt'] ?? 'Asset overview image' }}"
                                                class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-105"
                                                loading="lazy">

                                            {{-- Hover overlay for images --}}
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            </div>

                                        {{-- FALLBACK если нет медиа --}}
                                        @else
                                            <div class="flex items-center justify-center h-64 bg-gray-100 rounded-2xl">
                                                <div class="text-center text-gray-500">
                                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                              d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM6 6v12h12V6H6zM8 8h8v2H8V8zm0 4h8v2H8v-2z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-sm">No media available</p>
                                                </div>
                                            </div>
                                        @endif
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

{{-- resources/views/partials/product-card.blade.php --}}

<div class="w-full bg-white rounded-2xl overflow-hidden flex flex-col">
    <a href="{{ $productSummary['permalink'] }}" class="!no-underline">
        <div class="relative">

            {{-- Изображение товара с учетом вариаций --}}
            @php
                // Определяем изображение для отображения
                $displayImage = null;

                // Сначала пытаемся взять основное изображение товара
                if ($productSummary['image']) {
                    $displayImage = $productSummary['image'];
                }
                // Если основного изображения нет, но есть вариации с изображениями
                elseif ($productSummary['variations']) {
                    foreach ($productSummary['variations'] as $variation) {
                        if ($variation['image']) {
                            $displayImage = $variation['image'];
                            break; // Берем первое найденное изображение вариации
                        }
                    }
                }

                // Определяем URL изображения с fallback'ами
                $imageUrl = '';
                if ($displayImage) {
                    // Приоритет размеров для карточки товара
                    $imageUrl = $displayImage['large']['url'] ?? $displayImage['full']['url'] ?? '';
                }

                // Alt текст
                $altText = $displayImage['alt'] ?? $productSummary['title'] ?? 'Product image';
            @endphp

            @if($imageUrl)
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $altText }}"
                    class="w-full min-h-87.5 object-cover rounded-2xl"
                    loading="lazy"
                >
            @else
                {{-- Placeholder если нет изображения --}}
                <div class="w-full min-h-87.5 bg-gray-200 rounded-2xl flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif

            {{-- Label --}}
            @if($productAcfFields['label'])
                @php
                    $labelColor = $productAcfFields['label'] === 'easy_adaptable' ? 'bg-[#7700ff]' : 'bg-[#f62459]';
                @endphp
                <span class="absolute top-3 left-3 {{ $labelColor }} text-white font-semibold px-2 py-1 rounded-2xl leading-3 {{ $productAcfFields['label'] }}">
                    {{ ucfirst(str_replace('_', ' ', $productAcfFields['label'])) }}
                </span>
            @endif

            {{-- Флаг страны --}}
            <span class="absolute bottom-3 left-3 rounded-full overflow-hidden">
                <img src="{{ $productAcfFields['country_flag_url'] }}" alt="{{ $productAcfFields['country_code'] }}"
                    class="size-6.5 object-cover">
            </span>
        </div>

        <div class="pt-2.5 pb-4 px-3 flex flex-col flex-1">
            <h3 class="text-xl font-bold mb-1.5">{{ $productSummary['title'] }}</h3>

            <div class="concept-meta space-y-1.5 mb-3.5 text-sm">
                <p><span class="font-bold">Target:</span> {{ $productAcfFields['target'] }}</p>
                <p>
                    <span class="font-bold">Year:</span> {{ $productAcfFields['year'] }} |
                    <span class="font-bold">Buyout:</span> {{ $productAcfFields['buyout'] }}
                </p>
            </div>

            <div class="mt-auto flex justify-between items-center">
                {{-- Tags --}}
                <span
                    class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border border-purple-600">
                    {{-- {{ $tag }} --}}
                </span>

                {{-- Рейтинг товара --}}
                <div class="">
                    <img src="{{ Vite::asset('resources/images/demo/stars.webp') }}" alt="Stars"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </a>
</div>

<br>
<br>
<br>
<br>

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-blue-600">ProductSummary данные:</h4>
      @dump($productSummary)
    </div>

    <div class="mb-4">
      <h4 class="font-semibold text-green-600">ProductAcfFields данные:</h4>
      @dump($productAcfFields)
    </div>

    <div class="mb-4">
      <h4 class="font-semibold text-purple-600">Все доступные переменные:</h4>
      @dump(get_defined_vars())
    </div> --}}

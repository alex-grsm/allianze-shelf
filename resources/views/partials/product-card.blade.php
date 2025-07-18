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
                    $imageUrl = $displayImage['medium']['url'] ?? ($displayImage['full']['url'] ?? '');
                }

                // Alt текст
                $altText = $displayImage['alt'] ?? ($productSummary['title'] ?? 'Product image');
            @endphp

            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $altText }}" class="w-full min-h-87.5 object-cover rounded-2xl max-h-[350px]"
                    loading="lazy">
            @else
                {{-- Placeholder если нет изображения --}}
                <div class="w-full min-h-87.5 bg-gray-200 rounded-2xl flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            @endif

            {{-- Label (только если поле существует и не пустое) --}}
            @if (
                !empty($productAcfFields['label']) ||
                    !empty($productAcfFields['sma_label']) ||
                    !empty($productAcfFields['newsletter_label']) ||
                    !empty($productAcfFields['landing_page_label']))
                @php
                    $label =
                        $productAcfFields['label'] ??
                        ($productAcfFields['sma_label'] ??
                            ($productAcfFields['newsletter_label'] ?? ($productAcfFields['landing_page_label'] ?? '')));
                    $labelColor = $label === 'easy_adaptable' ? 'bg-[#7700ff]' : 'bg-[#f62459]';
                @endphp
                <span
                    class="absolute top-3 left-3 {{ $labelColor }} text-white font-semibold px-2 py-1 rounded-2xl leading-3 {{ $label }}">
                    {{ ucfirst(str_replace('_', ' ', $label)) }}
                </span>
            @endif

            {{-- Флаг страны (всегда показываем с fallback) --}}
            @php
                $countryCodes =
                    $productAcfFields['country_codes'] ??
                    ($productAcfFields['sma_country_codes'] ??
                        ($productAcfFields['newsletter_country_codes'] ??
                            ($productAcfFields['landing_page_country_codes'] ?? [])));

                $countryFlagsUrls =
                    $productAcfFields['country_flags_urls'] ??
                    ($productAcfFields['sma_country_flags_urls'] ??
                        ($productAcfFields['newsletter_country_flags_urls'] ??
                            ($productAcfFields['landing_page_country_flags_urls'] ?? [])));

                // Fallback на старые поля для совместимости
                if (empty($countryCodes)) {
                    $oldCountryCode =
                        $productAcfFields['country_code'] ??
                        ($productAcfFields['sma_country_code'] ??
                            ($productAcfFields['newsletter_country_code'] ??
                                ($productAcfFields['landing_page_country_code'] ?? '')));

                    if (!empty($oldCountryCode)) {
                        $countryCodes = [$oldCountryCode];
                        $countryFlagsUrls = [flag_url($oldCountryCode)];
                    }
                }

                // Убеждаемся что это массивы
                if (!is_array($countryCodes)) {
                    $countryCodes = !empty($countryCodes) ? [$countryCodes] : [];
                }
                if (!is_array($countryFlagsUrls)) {
                    $countryFlagsUrls = !empty($countryFlagsUrls) ? [$countryFlagsUrls] : [];
                }

                // Убираем пустые значения
                $countryCodes = array_filter($countryCodes);
                $countryFlagsUrls = array_filter($countryFlagsUrls);

                // Ограничиваем количество флагов для карточки
                $maxFlags = 10;
                $displayCodes = array_slice($countryCodes, 0, $maxFlags);
                $displayFlagsUrls = array_slice($countryFlagsUrls, 0, $maxFlags);
                $moreCount = max(0, count($countryCodes) - $maxFlags);
            @endphp

            <div class="absolute bottom-3 left-3 flex items-center space-x-1">
                @if (!empty($displayFlagsUrls))
                    @foreach ($displayFlagsUrls as $index => $flagUrl)
                        <span class="rounded-full overflow-hidden {{ $index > 0 ? '-ml-1.5' : '' }}" style="z-index: {{ 10 - $index }}">
                            <img src="{{ $flagUrl }}"
                                 alt="{{ $displayCodes[$index] ?? 'Country flag' }}"
                                 class="size-6.5 object-cover"
                                 title="{{ $displayCodes[$index] ?? '' }}">
                        </span>
                    @endforeach
                @else
                    {{-- Fallback флаг --}}
                    <span class="rounded-full overflow-hidden">
                        <img src="{{ flag_url('') }}" alt="Default flag" class="size-6.5 object-cover">
                    </span>
                @endif
            </div>
        </div>

        <div class="pt-2.5 pb-4 px-3 flex flex-col flex-1">
            <h3 class="text-xl font-bold mb-1.5">{{ $productSummary['title'] }}</h3>

            <div class="concept-meta space-y-1.5 mb-3.5 text-sm">
                {{-- Target (только если поле доступно) --}}
                @php
                    $target =
                        $productAcfFields['target'] ??
                        ($productAcfFields['sma_target'] ??
                            ($productAcfFields['newsletter_target'] ??
                                ($productAcfFields['landing_page_target'] ?? '')));
                @endphp
                @if (!empty($target))
                    <p><span class="font-bold">Target:</span> {{ $target }}</p>
                @endif

                {{-- Year и Buyout (только если поля доступны) --}}
                @php
                    $year =
                        $productAcfFields['year'] ??
                        ($productAcfFields['sma_year'] ??
                            ($productAcfFields['newsletter_year'] ?? ($productAcfFields['landing_page_year'] ?? '')));
                    $buyout =
                        $productAcfFields['buyout'] ??
                        ($productAcfFields['sma_buyout'] ??
                            ($productAcfFields['newsletter_buyout'] ??
                                ($productAcfFields['landing_page_buyout'] ?? '')));
                @endphp
                @if (!empty($year) || !empty($buyout))
                    <p>
                        @if (!empty($year))
                            <span class="font-bold">Year:</span> {{ $year }}
                        @endif

                        @if (!empty($year) && !empty($buyout))
                            |
                        @endif

                        @if (!empty($buyout))
                            <span class="font-bold">Buyout:</span> {{ $buyout }}
                        @endif
                    </p>
                @endif
            </div>

            <div class="mt-auto flex justify-between items-center">
                {{-- Тег из Product Tags Hierarchy --}}
                @php
                    // Получаем первый тег из кастомной таксономии ProductTagsHierarchy
                    $firstTag = '';
                    if ($productSummary['id']) {
                        $productTags = get_product_card_tags($productSummary['id'], 1);
                        if (!empty($productTags)) {
                            $firstTag = $productTags[0]['name'];
                        }
                    }
                @endphp
                <span
                    class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full border border-yellow-300">
                    {{ $firstTag ?: 'No tag' }}
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

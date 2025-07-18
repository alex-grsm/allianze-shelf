{{-- resources/views/partials/single-product/product-summary.blade.php --}}

@php
    // Получаем унифицированные мета-данные продукта через helper
    $productMeta = get_product_meta_data($productSummary['id']);
@endphp

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-blue-600">ProductSummary данные:</h4>
      @dump($productSummary)
    </div> --}}

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-green-600">ProductAcfFields данные:</h4>
      @dump($productAcfFields)
    </div> --}}

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-purple-600">Все доступные переменные:</h4>
      @dump(get_defined_vars())
    </div> --}}

<section class="py-20">
    <div class="container-fluid">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-start">
            <!-- Левая колонка - Изображение -->
            <div class="top-8">
                <div class="overflow-hidden relative">
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="w-full">
                            {{-- Галерея продукта --}}
                            @include('partials.single-product.product-gallery', [
                                'productSummary' => $productSummary,
                            ])
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка - Информация о продукте -->
            <div class="xl:pr-[calc((100vw-1280px)/2+10px)] mt-12.5">

                <!-- Мета информация -->
                <div class="flex mb-7">
                    {{-- Блок с флагом страны --}}
                    <div class="flex items-start flex-col gap-2 pr-8">
                        <span class="text-blue-600 text-xs font-bold">
                            Origin
                            @if (!empty($productMeta['country_codes']) && count($productMeta['country_codes']) > 1)
                                ({{ count($productMeta['country_codes']) }} countries)
                            @elseif (!empty($productMeta['primary_country_code']))
                                {{ $productMeta['primary_country_code'] }}
                            @endif
                        </span>

                        {{-- Отображение флагов --}}
                        <div class="flex items-center space-x-3">
                            @if (!empty($productMeta['country_flags_urls']))
                                @php
                                    $maxFlags = 10; // Максимум флагов для отображения
                                    $displayFlags = array_slice($productMeta['country_flags_urls'], 0, $maxFlags);
                                @endphp

                                @foreach ($displayFlags as $index => $flagUrl)
                                    <span class="rounded-full overflow-hidden {{ $index > 0 ? '-ml-1' : '' }}"
                                          style="z-index: {{ 10 - $index }}"
                                          title="{{ $productMeta['country_codes'][$index] ?? '' }}">
                                        <img src="{{ $flagUrl }}"
                                             alt="{{ $productMeta['country_codes'][$index] ?? 'Country flag' }}"
                                             class="size-6.5 object-cover">
                                    </span>
                                @endforeach
                            @else
                                {{-- Fallback флаг --}}
                                <span class="rounded-full overflow-hidden flex">
                                    <img src="{{ $productMeta['primary_country_flag_url'] }}"
                                         alt="{{ $productMeta['primary_country_code'] ?: 'Default flag' }}"
                                         class="size-6.5 object-cover">
                                </span>
                            @endif
                        </div>

                        {{-- Отображение названий стран при наведении --}}
                        @if (!empty($productMeta['countries_display']) && count($productMeta['countries_display']) > 1)
                            <div class="text-xs text-gray-500 mt-1" title="{{ implode(', ', $productMeta['countries_display']) }}">
                                {{ count($productMeta['countries_display']) }} countries
                            </div>
                        @endif
                    </div>

                    <!-- Разделитель -->
                    <div class="h-12 w-px bg-[#d3d3d3]"></div>

                    <!-- Content type -->
                    <div class="flex items-start flex-col gap-2 px-8">
                        <span class="text-blue-600 text-xs font-bold">Content type</span>
                        <span class="text-blue-600">{{ $productMeta['content_type_label'] }}</span>
                    </div>

                    <!-- Разделитель -->
                    <div class="h-12 w-px bg-[#d3d3d3]"></div>

                    <!-- Product -->
                    <div class="flex items-start flex-col gap-2 px-8">
                        <span class="text-blue-600 text-xs font-bold">Product</span>
                        <span class="text-blue-600">{{ $productMeta['product_name'] }}</span>
                    </div>
                </div>

                <!-- Заголовок и название кампании -->
                <div>
                    <span class="text-xs font-bold text-blue-600 block mb-2">Campaign Name</span>
                    <h1 class="text-3xl lg:text-4xl font-bold text-blue-600 mb-4">
                        {{ $productSummary['title'] }}
                    </h1>

                    <!-- Описание -->
                    <div class="text-blue-600 mb-5 text-sm max-w-sm">
                        {!! $productSummary['short_description'] !!}
                    </div>
                </div>

                <!-- Детали кампании -->
                <div class="text-blue-600 mb-8 text-sm product-description leading-normal">
                    {!! $productSummary['description'] !!}
                </div>

                <!-- Цены и покупка -->
                <div class="mb-4">
                    <!-- Доступность и форма покупки -->
                    <div class="space-y-4"
                         x-data="cartHandler({{ json_encode($productSummary['variations'] ?? []) }})"
                         data-product-cart
                         data-product-id="{{ $productSummary['id'] }}"
                         data-nonce="{{ wp_create_nonce('add_to_cart') }}"
                         data-ajax-url="{{ admin_url('admin-ajax.php') }}">

                        {{-- Локальные уведомления для вариаций --}}
                        <x-local-alert />

                        @if ($productSummary['type'] === 'variable')
                            <div class="flex flex-col">
                                <div class="flex gap-5 mb-4">
                                    @foreach ($productSummary['variations'] as $variation)
                                        <div class="border-2 rounded-lg px-5 py-4 flex flex-col justify-between cursor-pointer transition-colors max-w-56"
                                            :class="selectedVariation === {{ $variation['id'] }} ? 'border-blue-600' : 'border-[#e9e5e5]'"
                                            @click="selectVariation({{ $variation['id'] }})">
                                            <div class="font-bold mb-1 text-xs">
                                              {{ $loop->first ? 'Buyout costs' : 'Compensation Costs' }}
                                            </div>
                                            <div class="text-xs mb-3">
                                              @foreach ($variation['attributes'] as $attribute_name => $attribute_value)
                                                  @if (!empty($attribute_value) && strtolower($attribute_value) !== 'none')
                                                      <span class="attribute">{{ str_replace('-', ' ', $attribute_value) }}</span>
                                                  @endif
                                              @endforeach
                                            </div>
                                            <div class="text-xl font-medium">
                                                {{ $variation['regular_price'] }} €
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Блок с датой действия прав (используем helper) --}}
                                @if (!empty($productMeta['rights_until_formatted']))
                                    <div class="mb-8">
                                        <div class="text-xs mb-1 font-bold">Rights available until</div>
                                        <div>{{ $productMeta['rights_until_formatted'] }}</div>
                                    </div>
                                @endif

                                <div class="flex items-center gap-4">
                                    <button type="button" @click="addToCart()"
                                        :disabled="loading || !selectedVariation"
                                        :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800'"
                                        class="flex-1 bg-blue-600 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none">
                                        <span x-show="!loading">Add to cart</span>
                                        <span x-show="loading" class="flex items-center justify-center gap-2">
                                            <x-svg-icon name="loader" class="animate-spin w-5 h-5" />
                                            Loading...
                                        </span>
                                    </button>

                                    <a href="{{ get_permalink(get_page_by_path('contact')) }}" target="_blank"
                                        class="!no-underline flex-1 bg-blue-600 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none">
                                        <span class="flex items-center justify-center gap-2">
                                            Submit cost request
                                        </span>
                                    </a>

                                    <button @click="toggleWishlist()"
                                        x-bind:class="isInWishlist ? 'border-red-300 bg-red-50' : 'border-gray-300'"
                                        class="p-4 rounded-full border hover:border-gray-400 transition-colors">
                                        <span class="transition-transform duration-200">
                                            <x-svg-icon name="heart" x-show="!isInWishlist" />
                                            <x-svg-icon name="heart-red" x-show="isInWishlist" />
                                        </span>
                                    </button>

                                    <button @click="shareProduct()"
                                        class="p-4 rounded-full border border-gray-300 hover:border-gray-400 transition-colors">
                                        <x-svg-icon name="share" class="transition-transform duration-200" />
                                 </button>
                                </div>
                            </div>
                        @else
                            <button type="button" @click="addToCart({{ $productSummary['id'] }})"
                                :disabled="loading"
                                :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800'"
                                class="flex-1 bg-blue-600 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none">
                                <span x-show="!loading">Add to cart</span>
                                <span x-show="loading" class="flex items-center justify-center gap-2">
                                    <x-svg-icon name="loader" class="animate-spin w-5 h-5" />
                                    Loading...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

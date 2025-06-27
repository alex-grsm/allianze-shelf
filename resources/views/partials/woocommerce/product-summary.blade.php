{{-- resources/views/partials/product-data.blade.php --}}

@dump($productSummary ?? 'productSummary не определена')

{{-- --------------------------------------------------------------- --}}
<section class="py-20">
    <div class="container-fluid">

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-start">
            <!-- Левая колонка - Изображение -->
            <div class="top-8">
                <div class="overflow-hidden relative">
                    <!-- Демо изображение с картонными фигурками -->
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-6xl mb-4">
                                <img src="{{ Vite::asset('resources/images/demo/single-prod-0.webp') }}"
                                    alt="Product Image" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка - Информация о продукте -->
            <div class=" xl:pr-[calc((100vw-1280px)/2+10px)] mt-12.5">

                <!-- Мета информация -->
                <div class="flex mb-7">
                    <!-- Origin OE -->
                    <div class="flex items-start flex-col gap-2 pr-8">
                        <span class="text-blue-600 text-xs font-bold">Origin OE</span>
                        <span class="rounded-full overflow-hidden flex">
                            <img src="{{ flag_url('DE' ?? '') }}" alt="Flag" class="size-6.5 object-cover">
                        </span>
                    </div>

                    <!-- Разделитель -->
                    <div class="h-12 w-px bg-[#d3d3d3]"></div>

                    <!-- Content type -->
                    <div class="flex items-start flex-col gap-2 px-8">
                        <span class="text-blue-600 text-xs font-bold">Content type</span>
                        <span class="text-blue-600">
                            Brand campaign
                        </span>
                    </div>

                    <!-- Разделитель -->
                    <div class="h-12 w-px bg-[#d3d3d3]"></div>

                    <!-- Product -->
                    <div class="flex items-start flex-col gap-2 px-8">
                        <span class="text-blue-600 text-xs font-bold">Product</span>
                        <span class="text-blue-600">
                            Car
                        </span>
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
                  <!-- Стоимость компенсации -->
@if ($productSummary['type'] === 'variable' && $productSummary['variations'])
    <div class="flex gap-5 mb-4" x-data="{ selectedVariation: null }">
        @foreach ($productSummary['variations'] as $variation)
            <div
                class="border-2 rounded-lg px-5 py-4 flex flex-col justify-between cursor-pointer transition-colors max-w-56"
                :class="selectedVariation === {{ $variation['id'] }} ? 'border-blue-600' : 'border-[#e9e5e5]'"
                @click="selectedVariation = {{ $variation['id'] }}"
            >
                @php
                    // Проверяем, есть ли непустые атрибуты
                    $hasNonEmptyAttributes = false;
                    $titleAttribute = 'Compensation Costs';

                    if ($variation['attributes']) {
                        foreach ($variation['attributes'] as $name => $value) {
                            if (!empty($value)) {
                                $hasNonEmptyAttributes = true;
                                $titleAttribute = $name;
                                break;
                            }
                        }
                    }
                @endphp

                <div class="font-bold mb-1 text-xs">{{ $titleAttribute }}</div>
                <div class="text-xs mb-3">
                    @if ($variation['attributes'])
                        @foreach ($variation['attributes'] as $attribute_name => $attribute_value)
                            @if (!empty($attribute_value))
                                <span class="attribute">{{ $attribute_value }}</span>
                            @endif
                        @endforeach
                    @endif
                </div>
                <div class="text-xl font-medium">
                    @if ($hasNonEmptyAttributes)
                        <span class="text-xs font-normal">from</span> {{ $variation['regular_price'] }} €
                    @else
                        {{ $variation['regular_price'] }} €
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="simple-product-price">
        {{ $productSummary['price'] }}
    </div>
@endif

                    <!-- Доступность и форма покупки -->
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            Rights available until 12/2026
                        </p>

                        <div x-data="{
                            quantity: 1,
                            showSuccess: false,
                            addToCart() {
                                this.showSuccess = true;
                                setTimeout(() => this.showSuccess = false, 3000);
                            }
                        }" class="space-y-4">

                            <!-- Кнопки действий -->
                            <div class="flex items-center gap-4">
                                <button @click="addToCart()"
                                    class="flex-1 bg-blue-900 hover:bg-blue-800 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                                    Add to cart
                                </button>

                                <button class="p-4 rounded-full border border-gray-300 hover:border-gray-400 transition-colors">
                                    <x-svg-icon name="heart" class="transition-transform duration-200" />
                                </button>

                                <button class="p-4 rounded-full border border-gray-300 hover:border-gray-400 transition-colors">
                                    <x-svg-icon name="share" class="transition-transform duration-200" />
                                </button>
                            </div>

                            <!-- Уведомление об успехе -->
                            <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span>Product added to cart!</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Дополнительные стили для демо -->
<style>
    /* Кастомные стили для инпута количества */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    /* Анимация для кнопок */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

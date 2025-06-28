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
{{-- @if ($productSummary['type'] === 'variable' && $productSummary['variations'])
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
@endif --}}

                    <!-- Доступность и форма покупки -->
<div class="space-y-4" x-data="cartHandler({{ json_encode($productSummary['variations']) }})">
    <p class="text-sm text-gray-600">
        Rights available until 12/2026
    </p>


    @if ($productSummary['type'] === 'variable')
        <div class="flex flex-col gap-4">
            <div class="flex gap-5 mb-4">
                @foreach ($productSummary['variations'] as $variation)
                    <div
                        class="border-2 rounded-lg px-5 py-4 flex flex-col justify-between cursor-pointer transition-colors max-w-56"
                        :class="selectedVariation === {{ $variation['id'] }} ? 'border-blue-600' : 'border-[#e9e5e5]'"
                        @click="selectedVariation = {{ $variation['id'] }}"
                    >
                        <div class="font-bold mb-1 text-xs">Compensation Costs</div>
                        <div class="text-xs mb-3">
                            @foreach ($variation['raw_attributes'] as $attribute_name => $attribute_value)
                                @if (!empty($attribute_value))
                                    <span class="attribute">{{ $attribute_value }}</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="text-xl font-medium">
                            {{ $variation['regular_price'] }} €
                        </div>
                    </div>
                @endforeach
            </div>

            <button
                type="button"
                @click="addToCart(selectedVariation)"
                x-bind:disabled="!selectedVariation"
                class="flex-1 bg-blue-900 hover:bg-blue-800 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                Add to cart
            </button>
        </div>
    @else
        <button
            type="button"
            @click="addToCart({{ $productSummary['id'] }})"
            class="flex-1 bg-blue-900 hover:bg-blue-800 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
            Add to cart
        </button>
    @endif
</div>


                </div>

            </div>
        </div>
    </div>
</section>

<script>
function cartHandler(variations) {
    return {
        selectedVariation: null,

        async addToCart(variationId) {
            if (!variationId) {
                alert('Please select a variation.');
                return;
            }

            // Находим выбранную вариацию
            const variation = variations.find(v => v.id === variationId);
            if (!variation) {
                alert('Invalid variation');
                return;
            }

            // Создаем formData
            const formData = new FormData();
            formData.append('_wpnonce', '{{ wp_create_nonce("add_to_cart") }}');
            formData.append('action', 'woocommerce_ajax_add_to_cart');
            formData.append('product_id', variationId);
            formData.append('variation_id', variationId);

            // Добавляем атрибуты
            for (const [key, value] of Object.entries(variation.raw_attributes)) {
                formData.append(key, value);
            }

            try {
                const response = await fetch('{{ admin_url("admin-ajax.php") }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.error) {
                    alert(result.error);
                } else {
                    console.log('Product added:', result);
                    alert('Product added to cart!');
                }
            } catch (e) {
                console.error('Error adding to cart', e);
            }
        }
    }
}
</script>

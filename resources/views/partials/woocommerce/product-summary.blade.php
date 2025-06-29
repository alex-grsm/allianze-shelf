{{-- resources/views/partials/woocommerce/product-summary.blade.php --}}

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
                              @if($productSummary['image'])
                                  <img src="{{ $productSummary['image']['full']['url'] }}"
                                      alt="{{ $productSummary['image']['alt'] }}"
                                      width="{{ $productSummary['image']['full']['width'] }}"
                                      height="{{ $productSummary['image']['full']['height'] }}"
                                      class="w-full aspect-square object-cover">
                                @endif
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
                    <!-- Доступность и форма покупки -->
                    <div class="space-y-4"
                         x-data="cartHandler({{ json_encode($productSummary['variations'] ?? []) }})"
                         data-product-cart
                         data-product-id="{{ $productSummary['id'] }}"
                         data-nonce="{{ wp_create_nonce('add_to_cart') }}"
                         data-ajax-url="{{ admin_url('admin-ajax.php') }}"
                    >
                        {{-- Локальные уведомления для вариаций --}}
                        <x-local-alert />

                        @if ($productSummary['type'] === 'variable')
                            <div class="flex flex-col">
                                <div class="flex gap-5 mb-4">
                                    @foreach ($productSummary['variations'] as $variation)
                                        <div
                                            class="border-2 rounded-lg px-5 py-4 flex flex-col justify-between cursor-pointer transition-colors max-w-56"
                                            :class="selectedVariation === {{ $variation['id'] }} ? 'border-blue-600' : 'border-[#e9e5e5]'"
                                            @click="selectVariation({{ $variation['id'] }})"
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

                                <div class="mb-8">
                                    <div class="text-xs mb-1 font-bold">Rights available until</div>
                                    <div>12/2026</div>
                                </div>

                                <div class="flex items-center gap-4">
                                  <button
                                      type="button"
                                      @click="addToCart()"
                                      :disabled="loading || !selectedVariation"
                                      :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800'"
                                      class="flex-1 bg-blue-600 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none">
                                      <span x-show="!loading">Add to cart</span>
                                      <span x-show="loading" class="flex items-center justify-center gap-2">
                                          <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                          </svg>
                                          Loading...
                                      </span>
                                  </button>
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
                            <button
                                type="button"
                                @click="addToCart({{ $productSummary['id'] }})"
                                :disabled="loading"
                                :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800'"
                                class="flex-1 bg-blue-600 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none">
                                <span x-show="!loading">Add to cart</span>
                                <span x-show="loading" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
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

{{-- <script>
function cartHandler(variations) {
    return {
        selectedVariation: null,
        variationAlert: {
            show: false,
            type: 'info',
            message: ''
        },

        showVariationAlert(type, message) {
            this.variationAlert = {
                show: true,
                type: type,
                message: message
            };

            // Автоматически скрыть через 4 секунды
            clearTimeout(this.alertTimeout);
            this.alertTimeout = setTimeout(() => {
                this.hideVariationAlert();
            }, 4000);
        },

        hideVariationAlert() {
            this.variationAlert.show = false;
            clearTimeout(this.alertTimeout);
        },

        async addToCart(variationId) {
            // Скрываем предыдущие уведомления
            this.hideVariationAlert();

            if (!variationId) {
                this.showVariationAlert('warning', 'Please select a variation before adding to cart.');
                return;
            }

            const variation = variations.find(v => v.id === variationId);
            if (!variation) {
                this.showVariationAlert('error', 'The selected variation is not available.');
                return;
            }

            // Показываем индикатор загрузки
            this.showVariationAlert('info', 'Adding product to cart...');

            const formData = new FormData();
            formData.append('_wpnonce', '{{ wp_create_nonce("add_to_cart") }}');
            formData.append('action', 'woocommerce_ajax_add_to_cart');
            formData.append('product_id', variationId);
            formData.append('variation_id', variationId);

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
                    this.showVariationAlert('error', result.error);
                } else {
                    console.log('Product added:', result);
                    this.showVariationAlert('success', 'The product has been successfully added to your cart!');

                    // Обновить счетчик корзины, если функция существует
                    if (window.updateCartCount && typeof window.updateCartCount === 'function') {
                        window.updateCartCount();
                    }
                }
            } catch (e) {
                console.error('Error adding to cart', e);
                this.showVariationAlert('error', 'There was an error adding the item to your cart. Please try again.');
            }
        }
    }
}
</script> --}}

{{-- resources/views/partials/single-product/buyout-details.blade.php --}}

@if (
    !empty($buyoutDetails['has_content']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['companies', 'social_media_assets'])
)

    <section class="buyout-details-section bg-white py-20">
        <div class="container">
            <div class="buyout-details" x-data="{
                hasImage: {{ Js::from($buyoutDetails['has_image']) }},
                showImageModal: false,

                openImageModal() {
                    if (this.hasImage) {
                        this.showImageModal = true;
                    }
                },

                closeImageModal() {
                    this.showImageModal = false;
                }
            }">

                {{-- Заголовок --}}
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-blue-600 mb-4">
                        Buyout Details
                    </h2>

                    @if (!empty($buyoutDetails['description']))
                        <div class="text-blue-600 text-lg leading-relaxed max-w-6xl">
                            {!! nl2br(e($buyoutDetails['description'])) !!}
                        </div>
                    @endif
                </div>

                {{-- PNG таблица --}}
                @if ($buyoutDetails['has_image'])
                    <div class="buyout-table-section">
                        <div class="rounded-3xl relative overflow-hidden cursor-pointer max-w-[80%] mx-auto"
                            @click="openImageModal()">

                            <div class="relative z-10">
                                <img src="{{ $buyoutDetails['table_image']['sizes']['large'] ?? $buyoutDetails['table_image']['url'] }}"
                                    alt="{{ $buyoutDetails['table_alt'] }}"
                                    class="w-full h-auto max-h-122 object-cover mx-auto ">
                            </div>

                            {{-- Overlay для hover эффекта --}}
                            <div
                                class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100">
                                <div class="text-white text-lg font-medium flex items-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                        </path>
                                    </svg>
                                    <span>Click to view full size</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Модальное окно для просмотра изображения --}}
                <template x-if="hasImage">
                    <div x-show="showImageModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
                        style="display: none;" @click="closeImageModal()" @keydown.escape.window="closeImageModal()">

                        {{-- Backdrop --}}
                        <div class="fixed inset-0 bg-black/75 backdrop-blur-sm"></div>

                        {{-- Modal --}}
                        <div class="relative min-h-screen flex items-center justify-center p-4">
                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-6xl max-h-[90vh] overflow-hidden"
                                @click.stop>

                                {{-- Close button --}}
                                <button @click="closeImageModal()"
                                    class="absolute top-3 right-3 text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                {{-- Image --}}
                                <div class="">
                                    <img src="{{ $buyoutDetails['table_image']['url'] ?? '' }}"
                                        alt="{{ $buyoutDetails['table_alt'] }}"
                                        class="w-full h-auto max-h-[80vh] object-contain">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
@endif

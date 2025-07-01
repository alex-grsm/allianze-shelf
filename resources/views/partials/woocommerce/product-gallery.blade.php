{{--
Компонент галереи продукта
Принимает: $productSummary (массив с данными продукта)
--}}

@if($productSummary['gallery'] && !empty($productSummary['gallery']) || $productSummary['image'])
    @php
        // Собираем все изображения (основное + галерея)
        $all_images = [];

        // Добавляем основное изображение первым, если оно есть
        if ($productSummary['image']) {
            $all_images[] = $productSummary['image'];
        }

        // Добавляем изображения из галереи
        if ($productSummary['gallery'] && !empty($productSummary['gallery'])) {
            foreach ($productSummary['gallery'] as $gallery_image) {
                // Проверяем, чтобы не дублировать основное изображение
                if (!$productSummary['image'] || $gallery_image['id'] !== $productSummary['image']['id']) {
                    $all_images[] = $gallery_image;
                }
            }
        }

        $first_image = $all_images[0] ?? null;
        $has_multiple = count($all_images) > 1;
    @endphp

    <div class="product-gallery"
         x-data="{
            currentImage: 0,
            lightboxOpen: false,
            lightboxImage: 0,
            images: {{ json_encode($all_images) }},
            touchStartX: 0,
            touchEndX: 0,
            isTouch: false,
            lightboxTouchStartX: 0,
            lightboxTouchEndX: 0,
            isLightboxTouch: false,
            lastSwipeTime: 0,
            lastLightboxSwipeTime: 0,
            swipeDebounceDelay: 700,
            lightboxSwipeDebounceDelay: 500, // 500ms для lightbox (можно быстрее)
            isSwipeInProgress: false,
            isLightboxSwipeInProgress: false,

            nextImage() {
                this.currentImage = this.currentImage < this.images.length - 1 ? this.currentImage + 1 : 0;
            },

            prevImage() {
                this.currentImage = this.currentImage > 0 ? this.currentImage - 1 : this.images.length - 1;
            },

            nextLightboxImage() {
                this.lightboxImage = this.lightboxImage < this.images.length - 1 ? this.lightboxImage + 1 : 0;
            },

            prevLightboxImage() {
                this.lightboxImage = this.lightboxImage > 0 ? this.lightboxImage - 1 : this.images.length - 1;
            },

            openLightbox(index = null) {
                this.lightboxImage = index !== null ? index : this.currentImage;
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeLightbox() {
                this.lightboxOpen = false;
                document.body.style.overflow = '';
            },

            handleKeydown(e) {
                if (this.lightboxOpen) {
                    // Если lightbox открыт, управляем им
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        this.prevLightboxImage();
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        this.nextLightboxImage();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        this.closeLightbox();
                    }
                } else {
                    // Если lightbox закрыт, управляем основной галереей
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        this.prevImage();
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        this.nextImage();
                    }
                }
            },

            handleTouchStart(e) {
                if (this.isSwipeInProgress) return;
                this.touchStartX = e.changedTouches[0].screenX;
                this.isTouch = true;
            },

            handleTouchEnd(e) {
                if (!this.isTouch || this.isSwipeInProgress) return;

                this.touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe();
                this.isTouch = false;
            },

            handleLightboxTouchStart(e) {
                if (this.isLightboxSwipeInProgress) return;
                this.lightboxTouchStartX = e.changedTouches[0].screenX;
                this.isLightboxTouch = true;
            },

            handleLightboxTouchEnd(e) {
                if (!this.isLightboxTouch || this.isLightboxSwipeInProgress) return;

                this.lightboxTouchEndX = e.changedTouches[0].screenX;
                this.handleLightboxSwipe();
                this.isLightboxTouch = false;
            },

            handleSwipe() {
                const now = Date.now();

                // Проверяем debounce
                if (now - this.lastSwipeTime < this.swipeDebounceDelay) {
                    return;
                }

                const swipeThreshold = 50;
                const diff = this.touchStartX - this.touchEndX;

                if (Math.abs(diff) > swipeThreshold) {
                    this.isSwipeInProgress = true;
                    this.lastSwipeTime = now;

                    if (diff > 0) {
                        this.nextImage();
                    } else {
                        this.prevImage();
                    }

                    // Разблокируем через небольшую задержку
                    setTimeout(() => {
                        this.isSwipeInProgress = false;
                    }, 50);
                }
            },

            handleLightboxSwipe() {
                const now = Date.now();

                // Проверяем debounce для lightbox
                if (now - this.lastLightboxSwipeTime < this.lightboxSwipeDebounceDelay) {
                    return;
                }

                const swipeThreshold = 50;
                const diff = this.lightboxTouchStartX - this.lightboxTouchEndX;

                if (Math.abs(diff) > swipeThreshold) {
                    this.isLightboxSwipeInProgress = true;
                    this.lastLightboxSwipeTime = now;

                    if (diff > 0) {
                        this.nextLightboxImage();
                    } else {
                        this.prevLightboxImage();
                    }

                    // Разблокируем через небольшую задержку
                    setTimeout(() => {
                        this.isLightboxSwipeInProgress = false;
                    }, 50);
                }
            },

            handleWheel(e) {
                if (this.images.length <= 1) return;

                const now = Date.now();

                // Проверяем debounce для колесика мыши
                if (now - this.lastSwipeTime < this.swipeDebounceDelay) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();

                this.lastSwipeTime = now;

                if (e.deltaY > 0) {
                    this.nextImage();
                } else {
                    this.prevImage();
                }
            }
         }"
         @keydown.window="handleKeydown($event)"
         tabindex="0">

        {{-- Основное изображение --}}
        <div class="main-image-container relative overflow-hidden cursor-pointer"
            @touchstart.passive="handleTouchStart($event)"
            @touchend.passive="handleTouchEnd($event)"
            @touchmove.passive="() => {}"
            @wheel="handleWheel($event)"
            @click="openLightbox()">
            @if($first_image)
                <div class="slider-wrapper flex transition-transform duration-300 ease-out"
                    :style="`transform: translateX(-${currentImage * 100}%)`">
                    <template x-for="(image, index) in images" :key="index">
                        <div class="slide w-full flex-shrink-0">
                            <img :src="image.full.url"
                                :alt="image.alt || '{{ $productSummary['title'] }}'"
                                :width="image.full.width"
                                :height="image.full.height"
                                class="w-full h-full object-contain aspect-square select-none bg-black"
                                draggable="false">
                        </div>
                    </template>
                </div>
            @endif

            @if($has_multiple)
                {{-- Счетчик изображений --}}
                <div class="absolute top-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm pointer-events-none">
                    <span x-text="currentImage + 1"></span> / <span x-text="images.length"></span>
                </div>
            @endif

            {{-- Иконка увеличения --}}
            <div class="absolute top-4 left-4 bg-black/50 text-white p-2 rounded-full backdrop-blur-sm pointer-events-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
            </div>
        </div>

        {{-- Lightbox --}}
        <div x-show="lightboxOpen"
             x-transition.opacity.duration.300ms
             class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center"
             @click.self="closeLightbox()"
             @touchstart.passive="handleLightboxTouchStart($event)"
             @touchend.passive="handleLightboxTouchEnd($event)"
             @touchmove.passive="() => {}">

            {{-- Lightbox изображение --}}
            <div class="relative max-w-full max-h-full p-4">
                <img :src="images[lightboxImage].full.url"
                     :alt="images[lightboxImage].alt || '{{ $productSummary['title'] }}'"
                     class="max-w-full max-h-full object-contain select-none"
                     draggable="false">

                {{-- Кнопка закрытия --}}
                <button @click="closeLightbox()"
                        class="absolute top-6 right-6 text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                {{-- Навигация в lightbox --}}
                <template x-if="images.length > 1">
                    <div>
                        <button @click="prevLightboxImage()"
                                class="absolute left-6 top-1/2 transform -translate-y-1/2 text-white bg-black/50 hover:bg-black/70 rounded-full p-3 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <button @click="nextLightboxImage()"
                                class="absolute right-6 top-1/2 transform -translate-y-1/2 text-white bg-black/50 hover:bg-black/70 rounded-full p-3 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        {{-- Счетчик в lightbox --}}
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black/50 px-4 py-2 rounded-full text-sm backdrop-blur-sm">
                            <span x-text="lightboxImage + 1"></span> / <span x-text="images.length"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

@else
    {{-- Нет ни галереи, ни основного изображения - показываем заглушку --}}
    <div class="product-gallery placeholder">
        <img src="{{ home_url('/wp-content/uploads/woocommerce-placeholder.png') }}"
             alt="Изображение недоступно"
             class="w-full h-full object-cover opacity-50 aspect-square">
    </div>
@endif


    <!-- Секция продукта -->
    <section class="py-24">
        <div class="container-fluid">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-start">

                <!-- Левая колонка - Изображение -->
                <div class="lg:sticky lg:top-8">
                    <div class=" bg-gradient-to-br from-blue-100 to-blue-200 overflow-hidden relative">
                        <!-- Демо изображение с картонными фигурками -->
                        <div class="w-full h-full flex items-center justify-center p-8">
                            <div class="text-center">
                                <div class="text-6xl mb-4">
                                        <img src="{{ Vite::asset('resources/images/demo/single-prod-1.webp') }}" alt="Product Image" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Правая колонка - Информация о продукте -->
                <div class="space-y-8 xl:pr-[calc((100vw-1280px)/2+10px)]">

                    <!-- Мета информация -->
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">Origin OE</span>
                            <div class="flex gap-1">
                                <!-- Флаги -->
                                <div class="w-5 h-5 rounded-full bg-black"></div>
                                <div class="w-5 h-5 rounded-full bg-blue-600"></div>
                            </div>
                        </div>
                        <span class="text-gray-400">|</span>
                        <span class="text-gray-600">Product campaign</span>
                        <span class="text-gray-400">|</span>
                        <span class="text-gray-600">Home</span>
                    </div>

                    <!-- Заголовок и название кампании -->
                    <div>
                        <span class="text-sm text-gray-600 block mb-2">Campaign Name</span>
                        <h1 class="text-4xl lg:text-5xl font-bold text-blue-900 mb-4">
                            Da für dein Leben
                        </h1>

                        <!-- Описание -->
                        <div class="text-lg text-gray-700 leading-relaxed">
                            <strong>Allianz AI Home Campaign:</strong> Cross OE collaboration for the Allianz home insurance, driven and executed with GenAI.
                        </div>
                    </div>

                    <!-- Детали кампании -->
                    <div class="space-y-4">
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Year:</strong> 2024
                                </div>
                            </li>

                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Target audience:</strong> 18-27, 25-40, 40-60 years
                                </div>
                            </li>

                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Business objectives:</strong> Drive brand awareness for the home insurance of Allianz in Germany and France with a collaborative and AI-generated campaign.
                                </div>
                            </li>
                                <div>
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-1">•</span>
                                        <div>
                                            <strong>Activity summary:</strong> Placements on Instagram, Facebook, TikTok and Pinterest with static images and video content and a corresponding landingpage for further information.
                                        </div>
                                    </li>

                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-1">•</span>
                                        <div>
                                            <strong>KPIs:</strong> Brand awareness (Views, Impressions)
                                        </div>
                                    </li>

                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-1">•</span>
                                        <div>
                                            <strong>Agency:</strong> Belly (Germany), Ogilvy (France)
                                        </div>
                                    </li>
                        </ul>
                    </div>

                    <!-- Цены и покупка -->
                    <div class="border-t pt-8 space-y-6">
                        <div class="grid sm:grid-cols-2 gap-6">
                            <!-- Стоимость покупки -->
                            <div class="bg-blue-50 rounded-xl p-6">
                                <h3 class="font-medium text-gray-700 mb-1">Buyout costs</h3>
                                <p class="text-sm text-gray-600 mb-3">1 year / digital channels</p>
                                <div class="text-2xl font-bold text-blue-900">
                                    from 5.000€
                                </div>
                            </div>

                            <!-- Стоимость компенсации -->
                            <div class="bg-gray-50 rounded-xl p-6">
                                <h3 class="font-medium text-gray-700 mb-3">Compensation costs</h3>
                                <div class="text-2xl font-bold text-gray-900">
                                    €1.000€
                                </div>
                            </div>
                        </div>

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
                                <!-- Количество -->
                                <div class="flex items-center gap-4">
                                    <label class="text-sm font-medium text-gray-700">Quantity:</label>
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button
                                            @click="quantity = Math.max(1, quantity - 1)"
                                            class="p-2 hover:bg-gray-100 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input
                                            type="number"
                                            x-model="quantity"
                                            min="1"
                                            class="w-16 text-center border-0 focus:ring-0"
                                            readonly
                                        >
                                        <button
                                            @click="quantity++"
                                            class="p-2 hover:bg-gray-100 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Кнопки действий -->
                                <div class="flex items-center gap-4">
                                    <button
                                        @click="addToCart()"
                                        class="flex-1 bg-blue-900 hover:bg-blue-800 text-white font-medium py-4 px-8 rounded-full transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]"
                                    >
                                        Add to cart
                                    </button>

                                    <button
                                        x-data="{ liked: false }"
                                        @click="liked = !liked"
                                        class="p-4 rounded-full border border-gray-300 hover:border-gray-400 transition-colors"
                                    >
                                        <svg class="w-5 h-5 transition-colors" :class="liked ? 'text-red-500 fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                    </button>

                                    <button
                                        @click="navigator.share ? navigator.share({title: 'Da für dein Leben', url: window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                                        class="p-4 rounded-full border border-gray-300 hover:border-gray-400 transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a3 3 0 10-2.684-4.026m-9.032 0a3 3 0 002.684 4.026m9.032 0a3 3 0 10-2.684 4.026M7 20a3 3 0 100-6 3 3 0 000 6z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Уведомление об успехе -->
                                <div
                                    x-show="showSuccess"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
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
            0%, 100% {
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

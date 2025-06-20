<!-- Support Section -->
<section class="py-25 bg-[linear-gradient(135deg,#f3f4f6_0%,#d2d2d2_50%,#bfbfbf_100%)]">
    <div class="container">

        <div class="">
            {{-- Заголовок секции --}}
            <div class="mb-6">
                <h2 class="text-3xl lg:text-4xl">
                    Related products
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 w-full mt-5">
                    <!-- Life Block -->
                    <div class="relative h-82.5 rounded-2xl overflow-hidden bg-cover bg-center"
                        style="background-image: url('{{ Vite::asset('resources/images/demo/related-1.webp') }}')">
                        <!-- Overlay для лучшей читаемости текста -->
                        <div class="absolute inset-0 bg-black/30"></div>

                        <!-- Контент блока -->
                        <div class="relative h-full p-6 lg:p-7 flex flex-col justify-between">
                            <!-- Заголовок -->
                            <h2 class="text-4xl lg:text-5xl xl:text-6xl text-white leading-3 tracking-tight">
                                Life
                            </h2>

                            <!-- Кнопка -->
                            <div class="self-start">
                                <a href="#details"
                                    class="!no-underline px-2 py-1 inline-flex items-center bg-white rounded-full font-bold min-h-8 min-w-28 justify-center">
                                    See details →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Health Block -->
                    <div class="relative h-82.5 rounded-2xl overflow-hidden bg-cover bg-center"
                        style="background-image: url('{{ Vite::asset('resources/images/demo/related-2.webp') }}')">
                        <!-- Overlay для лучшей читаемости текста -->
                        <div class="absolute inset-0 bg-black/30"></div>

                        <!-- Контент блока -->
                        <div class="relative h-full p-6 lg:p-7 flex flex-col justify-between">
                            <!-- Заголовок -->
                            <h2 class="text-4xl lg:text-5xl xl:text-6xl text-white leading-3 tracking-tight">
                                Health
                            </h2>

                            <!-- Кнопка -->
                            <div class="self-start">
                                <a href="#details"
                                    class="!no-underline px-2 py-1 inline-flex items-center bg-white rounded-full font-bold min-h-8 min-w-28 justify-center">
                                    See details →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

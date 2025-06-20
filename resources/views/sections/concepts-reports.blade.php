{{-- concepts-reports.blade.php --}}
<section class="py-16 lg:py-25">
    <div class="pl-4 md:pl-6 lg:pl-8 xl:pl-[calc((100vw-1280px)/2+10px)]">
        {{-- Заголовок секции --}}
        <div class="mb-7.5">
            <h2 class="text-3xl lg:text-4xl">
                Global Concepts and Reports
            </h2>
        </div>

        {{-- Слайдер карточек --}}
        <div class="crop-concepts-slider">
            <div class="swiper-wrapper">

                {{-- Vista Marketing Proposition --}}
                <div class="swiper-slide">
                    <div class="concept-card bg-white rounded-2xl p-3">
                        {{-- Иконка --}}
                        <div class="icon-wrapper size-14 flex items-center justify-center mb-1.5">
                            <img src="{{ Vite::asset('resources/images/icons/concept/icon-1.svg') }}"
                                alt="Vista Marketing Proposition" class="size-14">
                        </div>

                        {{-- Контент --}}
                        <h3 class="text-lg font-bold mb-1.5">
                            Vista Marketing Proposition
                        </h3>

                        <div class="concept-meta space-y-1.5 mb-4">
                            <p class="leading-3">
                                <span class="font-bold">Target:</span> Marketing, Brand, Creative
                            </p>
                            <p class="leading-3">
                                <span class="font-bold">Year:</span> 2023 |
                                <span class="font-bold">Buyout:</span> 300€*
                            </p>
                        </div>

                        {{-- Тег --}}
                        <a href="#" class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border-1 border-purple-600">
                            Campaign Concept
                        </a>
                    </div>
                </div>

                {{-- Global Trend Report --}}
                <div class="swiper-slide">
                    <div class="concept-card bg-white rounded-2xl p-3">
                        {{-- Иконка --}}
                        <div class="icon-wrapper size-14 flex items-center justify-center mb-1.5">
                            <img src="{{ Vite::asset('resources/images/icons/concept/icon-2.svg') }}" alt="Global Trend Report"
                                class="size-14">
                        </div>

                        {{-- Контент --}}
                        <h3 class="text-lg font-bold mb-1.5">
                            Global Trend Report
                        </h3>

                        <div class="concept-meta space-y-1.5 mb-4">
                            <p class="text-sm">
                                <span class="font-bold">Target:</span> Analysts, Strategists
                            </p>
                            <p class="text-sm">
                                <span class="font-bold">Year:</span> 2023 |
                                <span class="font-bold">Buyout:</span> 200€*
                            </p>
                        </div>

                        {{-- Тег --}}
                        <a href="#" class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border-1 border-purple-600">
                            Market Insights
                        </a>
                    </div>
                </div>

                {{-- Global GenZ Target Group --}}
                <div class="swiper-slide">
                    <div class="concept-card bg-white rounded-2xl p-3">
                        {{-- Иконка --}}
                        <div class="icon-wrapper size-14 flex items-center justify-center mb-1.5">
                            <img src="{{ Vite::asset('resources/images/icons/concept/icon-3.svg') }}"
                                alt="Global GenZ Target Group" class="size-14">
                        </div>

                        {{-- Контент --}}
                        <h3 class="text-lg font-bold mb-1.5">
                            Global GenZ Target Group
                        </h3>

                        <div class="concept-meta space-y-1.5 mb-4">
                            <p class="text-sm">
                                <span class="font-bold">Target:</span> Youth Marketing, Social Media
                            </p>
                            <p class="text-sm">
                                <span class="font-bold">Year:</span> 2023 |
                                <span class="font-bold">Buyout:</span> 300€*
                            </p>
                        </div>

                        {{-- Тег --}}
                        <a href="#" class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border-1 border-purple-600">
                            Youth Focus
                        </a>
                    </div>
                </div>

                {{-- Global Marketing Guideline --}}
                <div class="swiper-slide">
                    <div class="concept-card bg-white rounded-2xl p-3">
                        {{-- Иконка --}}
                        <div class="icon-wrapper size-14 flex items-center justify-center mb-1.5">
                            <img src="{{ Vite::asset('resources/images/icons/concept/icon-4.svg') }}"
                                alt="Global Marketing Guideline" class="size-14">
                        </div>

                        {{-- Контент --}}
                        <h3 class="text-lg font-bold mb-1.5">
                            Global Marketing Guideline
                        </h3>

                        <div class="concept-meta space-y-1.5 mb-4">
                            <p class="text-sm">
                                <span class="font-bold">Target:</span> Global Marketing, Brand
                            </p>
                            <p class="text-sm">
                                <span class="font-bold">Year:</span> 2023 |
                                <span class="font-bold">Buyout:</span> 400€*
                            </p>
                        </div>

                        {{-- Тег --}}
                        <a href="#" class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border-1 border-purple-600">
                            Brand Standards
                        </a>
                    </div>
                </div>

                {{-- OPM --}}
                <div class="swiper-slide">
                    <div class="concept-card bg-white rounded-2xl p-3">
                        {{-- Иконка --}}
                        <div class="icon-wrapper size-14 flex items-center justify-center mb-1.5">
                            <img src="{{ Vite::asset('resources/images/icons/concept/icon-5.svg') }}" alt="OPM"
                                class="size-14">
                        </div>

                        {{-- Контент --}}
                        <h3 class="text-lg font-bold mb-1.5">
                            OPM
                        </h3>

                        <div class="concept-meta space-y-1.5 mb-4">
                            <p class="text-sm">
                                <span class="font-bold">Target:</span> Sponsorship, Sports Marketing
                            </p>
                            <p class="text-sm">
                                <span class="font-bold">Year:</span> 2023 |
                                <span class="font-bold">Buyout:</span> 200€*
                            </p>
                        </div>

                        {{-- Тег --}}
                        <a href="#" class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border-1 border-purple-600">
                            Sport
                        </a>
                    </div>
                </div>

                {{-- OPM --}}
                <div class="swiper-slide">
                    <div class="concept-card bg-white rounded-2xl p-3">
                        {{-- Иконка --}}
                        <div class="icon-wrapper size-14 flex items-center justify-center mb-1.5">
                            <img src="{{ Vite::asset('resources/images/icons/concept/icon-5.svg') }}" alt="OPM"
                                class="size-14">
                        </div>

                        {{-- Контент --}}
                        <h3 class="text-lg font-bold mb-1.5">
                            OPM
                        </h3>

                        <div class="concept-meta space-y-1.5 mb-4">
                            <p class="text-sm">
                                <span class="font-bold">Target:</span> Sponsorship, Sports Marketing
                            </p>
                            <p class="text-sm">
                                <span class="font-bold">Year:</span> 2023 |
                                <span class="font-bold">Buyout:</span> 200€*
                            </p>
                        </div>

                        {{-- Тег --}}
                        <a href="#" class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border-1 border-purple-600">
                            Sport
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

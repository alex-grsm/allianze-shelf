<!-- Footer -->
<footer class="bg-black text-white pt-8 pb-4">
    <div class="container">
        <!-- Основной контент футера -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 items-center">
            <!-- Логотип -->
            <div class="lg:col-span-1">
                <div class="flex items-center">
            <img src="{{ Vite::asset('resources/images/logo-text.svg') }}">
                </div>
            </div>

            <!-- Все ссылки справа -->
            <div class="lg:col-span-2">
                <div class="flex items-center justify-end">
                    <ul class="flex flex-wrap gap-x-1 gap-y-2 items-center">
                        <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Faq</a></li>
                        <li class="text-white/75 text-sm mx-3">•</li>
                        <li><a href="{{ get_permalink(get_page_by_path('contact')) }}" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Contact help</a></li>
                        <li class="text-white/75 text-sm mx-3">•</li>
                        <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Customer Support</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Разделительная линия -->
        <div class="border-t border-white/75 pt-3">
            <div class="flex items-center justify-between">
                <span class="!no-underline text-white/75 text-xs">Copyright @<?php echo date_i18n('Y'); ?></span>
                  <div class="flex items-center justify-end">
                    <ul class="flex flex-wrap gap-x-1 gap-y-2 items-center">
                        <li><a href="#" class="!no-underline text-white/75 text-xs hover:text-white transition-colors">Privacy Policy</a></li>
                        <li class="text-white/75 text-xs mx-2">•</li>
                        <li><a href="#" class="!no-underline text-white/75 text-xs hover:text-white transition-colors">Terms and Conditions</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

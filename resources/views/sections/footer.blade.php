<!-- Footer -->
<footer class="bg-black text-white pt-18 pb-12">
    <div class="container">
        <!-- Основной контент футера -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mb-16">
            <!-- Логотип -->
            <div class="lg:col-span-1">
                <div class="flex items-center">
            <img src="{{ Vite::asset('resources/images/logo-text.svg') }}">
                </div>
            </div>

            <!-- Навигационные колонки -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:pl-42">
                    <!-- Company -->
                    <div>
                        <h3 class="font-semibold mb-3.5 text-white/75 tracking-tighter text-2xl">Company</h3>
                        <ul class="space-y-4">
                            <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">About Us</a></li>
                            <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Careers</a></li>
                            <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Pricing</a></li>
                        </ul>
                    </div>

                    <!-- Information -->
                    <div>
                        <h3 class="font-semibold mb-3.5 text-white/75 tracking-tighter text-2xl">Information</h3>
                        <ul class="space-y-4">
                            <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Faq</a></li>
                            <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Contact help</a></li>
                            <li><a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Customer Support</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Разделительная линия -->
        <div class="border-t border-white/75 pt-6">
            <!-- Нижняя часть -->
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <!-- Terms and Condition -->
                <div>
                    <a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Terms and Condition</a>
                </div>

                <!-- Copyright -->
                <div>
                    <span class="!no-underline text-white/75 text-sm">Copyright 2025</span>
                </div>

                <!-- Privacy Policy -->
                <div>
                    <a href="#" class="!no-underline text-white/75 text-sm hover:text-white transition-colors">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- <footer class="content-info">
  @php(dynamic_sidebar('sidebar-footer'))
</footer> --}}

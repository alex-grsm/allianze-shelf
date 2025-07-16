{{-- resources/views/components/header-search.blade.php --}}
<div class="relative"
     x-data="headerSearch()"
     x-init="init()"
     x-on:keydown.escape.window="closeSearch()">

    {{-- Search Toggle Button --}}
    <button x-on:click="toggleSearch()"
            class="flex items-center justify-center text-white hover:text-gray-300 p-2 rounded-md transition-colors duration-200">
        <x-svg-icon name="search" class="size-6" />
    </button>

    {{-- Search Overlay --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50"
         x-on:click="closeSearch()"
         style="display: none;">
    </div>

    {{-- Search Modal --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed top-20 left-1/2 transform -translate-x-1/2 w-full max-w-2xl z-50 px-4"
         style="display: none;">

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden"
             x-on:click.stop>

            {{-- Search Input --}}
            <div class="p-6 border-b border-gray-100">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-svg-icon name="search" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input x-ref="searchInput"
                           x-model="query"
                           x-on:input="handleInput()"
                           x-on:keydown.enter="performSearch()"
                           type="text"
                           class="block w-full pl-10 pr-4 py-4 text-lg border-0 bg-gray-50 rounded-xl text-blue-600 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                           placeholder="Search products..."
                           autocomplete="off">

                    {{-- Clear button --}}
                    <button x-show="query.length > 0"
                            x-on:click="clearSearch()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Search Results --}}
            <div x-show="shouldShowResults()" class="max-h-96 overflow-y-auto">
                {{-- Loading State --}}
                <div x-show="isLoading" class="p-6">
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-2 text-gray-600">Searching products...</span>
                    </div>
                </div>

                {{-- Results --}}
                <div x-show="!isLoading && hasValidResults()" class="divide-y divide-gray-100">
                    <template x-for="result in results" x-bind:key="result.id">
                        <a x-bind:href="result.url"
                           class="!no-underline block p-4 hover:bg-gray-50 transition-colors"
                           x-on:click="closeSearch()">
                            <div class="flex items-center space-x-3">
                                <div x-show="result.image" class="flex-shrink-0">
                                    <img x-bind:src="result.image"
                                         x-bind:alt="result.title"
                                         class="w-12 h-12 object-cover rounded-lg">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900" x-text="result.title"></div>
                                    <div class="text-sm text-gray-500" x-text="result.excerpt"></div>
                                    <div x-show="result.price" class="text-sm font-medium text-blue-600" x-text="result.regular_price"></div>
                                    <div x-show="window.searchDebug" class="text-xs text-gray-400" x-text="'Score: ' + (result.relevance_score || 'N/A')"></div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                {{-- No Results --}}
                <div x-show="!isLoading && shouldShowNoResults()" class="p-6 text-center">
                    <div class="text-gray-500">
                        <x-svg-icon name="search" class="mx-auto h-12 w-12 text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium mb-2">No products found</h3>
                        <p class="text-sm">Try different keywords or check spelling</p>
                    </div>
                </div>

                {{-- View All Results --}}
                <div x-show="!isLoading && hasValidResults()" class="p-4 border-t border-gray-100 bg-gray-50">
                    <button x-on:click="viewAllResults()"
                            class="w-full text-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                        View all results for "<span x-text="query"></span>"
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script>
function headerSearch() {
    return {
        isOpen: false,
        query: '',
        results: [],
        isLoading: false,
        debounceTimer: null,
        hasSearched: false,

        init() {
            // Проверяем доступность searchAjax
            if (!window.searchAjax) {
                console.warn('searchAjax not found, search functionality may be limited');
                window.searchAjax = {
                    ajax_url: window.location.origin + '/wp-admin/admin-ajax.php',
                    nonce: '',
                    home_url: window.location.origin
                };
            }

            // Включаем отладку если включен WP_DEBUG
            window.searchDebug = true; // Установите в true для отладки
        },

        toggleSearch() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            }
        },

        closeSearch() {
            this.isOpen = false;
            this.query = '';
            this.results = [];
            this.isLoading = false;
            this.hasSearched = false;
        },

        clearSearch() {
            this.query = '';
            this.results = [];
            this.hasSearched = false;
            if (this.$refs.searchInput) {
                this.$refs.searchInput.focus();
            }
        },

        // Проверяет, является ли запрос "тарабарщиной"
        isGibberish(query) {
            const cleanQuery = query.trim().toLowerCase();

            // Слишком короткий запрос
            if (cleanQuery.length < 2) return true;

            // Слишком длинный запрос (больше 50 символов)
            if (cleanQuery.length > 50) return true;

            // Проверяем на случайные символы (много согласных подряд)
            const consonantPattern = /[bcdfghjklmnpqrstvwxyz]{5,}/i;
            if (consonantPattern.test(cleanQuery)) return true;

            // Проверяем на повторяющиеся символы
            const repeatingPattern = /(.)\1{4,}/;
            if (repeatingPattern.test(cleanQuery)) return true;

            // Проверяем на только цифры или символы
            const onlyNumbersOrSymbols = /^[\d\W]+$/;
            if (onlyNumbersOrSymbols.test(cleanQuery)) return true;

            // Проверяем на хаотичный набор символов
            const chaotic = /^[^a-zA-Zа-яА-Я\d\s]{3,}$/;
            if (chaotic.test(cleanQuery)) return true;

            return false;
        },

        // Вычисляет релевантность на клиенте (дублирует серверную логику)
        calculateClientRelevance(result, searchTerm) {
            if (!result || !result.title || !searchTerm) return 0;

            let score = 0;
            const searchLower = searchTerm.toLowerCase().trim();
            const titleLower = result.title.toLowerCase();

            // Точное совпадение названия
            if (titleLower === searchLower) {
                score += 100;
            }
            // Название начинается с поискового запроса
            else if (titleLower.indexOf(searchLower) === 0) {
                score += 50;
            }
            // Поисковый запрос содержится в названии
            else if (titleLower.indexOf(searchLower) !== -1) {
                score += 25;
            }
            // Частичное совпадение слов
            else {
                const searchWords = searchLower.split(' ');
                const titleWords = titleLower.split(' ');

                searchWords.forEach(searchWord => {
                    if (searchWord.length < 3) return;

                    titleWords.forEach(titleWord => {
                        if (titleWord.indexOf(searchWord) !== -1) {
                            score += 10;
                        }
                    });
                });
            }

            // Проверяем описание
            if (result.excerpt) {
                const excerptLower = result.excerpt.toLowerCase();
                if (excerptLower.indexOf(searchLower) !== -1) {
                    score += 10;
                }
            }

            // Бонус за наличие товара
            if (result.in_stock) {
                score += 5;
            }

            // Бонус за изображение
            if (result.image) {
                score += 2;
            }

            return score;
        },

        // Фильтрует результаты по релевантности на клиенте
        filterResultsByRelevance(results, searchTerm, minScore = 5) {
            return results
                .map(result => ({
                    ...result,
                    client_relevance: this.calculateClientRelevance(result, searchTerm)
                }))
                .filter(result => result.client_relevance >= minScore)
                .sort((a, b) => b.client_relevance - a.client_relevance);
        },

        // Проверяет, нужно ли показывать результаты
        shouldShowResults() {
            return this.query.length >= 2 && !this.isGibberish(this.query);
        },

        // Проверяет, есть ли валидные результаты
        hasValidResults() {
            return this.results.length > 0 && this.hasSearched;
        },

        // Проверяет, нужно ли показывать "No results"
        shouldShowNoResults() {
            return this.hasSearched && this.results.length === 0 && this.shouldShowResults();
        },

        handleInput() {
            this.hasSearched = false;

            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            // Если запрос слишком короткий или "тарабарщина" - не ищем
            if (!this.shouldShowResults()) {
                this.results = [];
                this.isLoading = false;
                return;
            }

            this.debounceTimer = setTimeout(() => {
                this.searchProducts();
            }, 300);
        },

        async searchProducts() {
            // Повторно проверяем валидность запроса
            if (!this.shouldShowResults()) {
                this.results = [];
                this.isLoading = false;
                this.hasSearched = false;
                return;
            }

            this.isLoading = true;
            this.results = [];

            try {
                const searchAjax = window.searchAjax;
                const trimmedQuery = this.query.trim();

                // Формируем данные для отправки
                const formData = new FormData();
                formData.append('action', 'search_products');
                formData.append('s', trimmedQuery);
                formData.append('per_page', '10'); // Берем больше для клиентской фильтрации

                // Добавляем nonce только если он есть
                if (searchAjax.nonce) {
                    formData.append('nonce', searchAjax.nonce);
                }

                const response = await fetch(searchAjax.ajax_url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    let rawResults = data.data || [];

                    if (window.searchDebug) {
                        console.log('Raw server results:', rawResults);
                    }

                    // Дополнительная фильтрация и сортировка на клиенте
                    const filteredResults = this.filterResultsByRelevance(rawResults, trimmedQuery, 5);

                    // Берем только топ-6 результатов
                    this.results = filteredResults.slice(0, 6);

                    if (window.searchDebug) {
                        console.log('Filtered client results:', this.results);
                        this.results.forEach(result => {
                            console.log(`${result.title}: Server=${result.relevance_score || 'N/A'}, Client=${result.client_relevance}`);
                        });
                    }

                    this.hasSearched = true;
                } else {
                    console.error('Search failed:', data.data || 'Unknown error');
                    this.results = [];
                    this.hasSearched = true;

                    // Если ошибка безопасности, пробуем без nonce
                    if (data.data && typeof data.data === 'string' && data.data.includes('Security check')) {
                        await this.searchWithoutNonce();
                        return;
                    }
                }
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
                this.hasSearched = true;
            } finally {
                this.isLoading = false;
            }
        },

        async searchWithoutNonce() {
            try {
                const searchAjax = window.searchAjax;
                const trimmedQuery = this.query.trim();

                const formData = new FormData();
                formData.append('action', 'search_products');
                formData.append('s', trimmedQuery);
                formData.append('per_page', '10');

                const response = await fetch(searchAjax.ajax_url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    let rawResults = data.data || [];
                    const filteredResults = this.filterResultsByRelevance(rawResults, trimmedQuery, 5);
                    this.results = filteredResults.slice(0, 6);
                    this.hasSearched = true;
                } else {
                    console.error('Search without nonce failed:', data.data);
                    this.results = [];
                    this.hasSearched = true;
                }
            } catch (error) {
                console.error('Search without nonce error:', error);
                this.results = [];
                this.hasSearched = true;
            }
        },

        performSearch() {
            if (this.query.trim() && !this.isGibberish(this.query)) {
                const searchAjax = window.searchAjax;
                const baseUrl = searchAjax.home_url || window.location.origin;
                window.location.href = `${baseUrl}/?s=${encodeURIComponent(this.query.trim())}`;
            }
        },

        viewAllResults() {
            this.performSearch();
        }
    }
}
</script> --}}

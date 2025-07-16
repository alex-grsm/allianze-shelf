// resources/js/components/header-search.js

document.addEventListener('alpine:init', () => {
    Alpine.data('headerSearch', () => ({
        isOpen: false,
        query: '',
        results: [],
        isLoading: false,
        debounceTimer: null,
        hasSearched: false,

        init() {
            if (!window.searchAjax) {
                console.warn('searchAjax not found, search functionality may be limited');
                window.searchAjax = {
                    ajax_url: window.location.origin + '/wp-admin/admin-ajax.php',
                    nonce: '',
                    home_url: window.location.origin
                };
            }

            window.searchDebug = false;
        },

        toggleSearch() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus();
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
            this.$refs.searchInput?.focus();
        },

        isGibberish(query) {
            const clean = query.trim().toLowerCase();
            return (
                clean.length < 2 ||
                clean.length > 50 ||
                /[bcdfghjklmnpqrstvwxyz]{5,}/i.test(clean) ||
                /(.)\1{4,}/.test(clean) ||
                /^[\d\W]+$/.test(clean) ||
                /^[^a-zA-Zа-яА-Я\d\s]{3,}$/.test(clean)
            );
        },

        calculateClientRelevance(result, term) {
            if (!result || !result.title || !term) return 0;

            let score = 0;
            const query = term.toLowerCase().trim();
            const title = result.title.toLowerCase();

            if (title === query) score += 100;
            else if (title.startsWith(query)) score += 50;
            else if (title.includes(query)) score += 25;
            else {
                const qWords = query.split(' ');
                const tWords = title.split(' ');
                qWords.forEach(q => {
                    if (q.length >= 3) {
                        tWords.forEach(t => {
                            if (t.includes(q)) score += 10;
                        });
                    }
                });
            }

            if (result.excerpt?.toLowerCase().includes(query)) score += 10;
            if (result.in_stock) score += 5;
            if (result.image) score += 2;

            return score;
        },

        filterResultsByRelevance(results, term, min = 5) {
            return results
                .map(r => ({
                    ...r,
                    client_relevance: this.calculateClientRelevance(r, term)
                }))
                .filter(r => r.client_relevance >= min)
                .sort((a, b) => b.client_relevance - a.client_relevance);
        },

        shouldShowResults() {
            return this.query.length >= 2 && !this.isGibberish(this.query);
        },

        hasValidResults() {
            return this.results.length > 0 && this.hasSearched;
        },

        shouldShowNoResults() {
            return this.hasSearched && this.results.length === 0 && this.shouldShowResults();
        },

        handleInput() {
            this.hasSearched = false;
            clearTimeout(this.debounceTimer);

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
                const query = this.query.trim();

                const formData = new FormData();
                formData.append('action', 'search_products');
                formData.append('s', query);
                formData.append('per_page', '10');
                if (searchAjax.nonce) formData.append('nonce', searchAjax.nonce);

                const res = await fetch(searchAjax.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                if (!res.ok) throw new Error(`HTTP error: ${res.status}`);

                const data = await res.json();

                if (data.success) {
                    const raw = data.data || [];
                    const filtered = this.filterResultsByRelevance(raw, query);
                    this.results = filtered.slice(0, 6);
                    this.hasSearched = true;

                    if (window.searchDebug) {
                        console.log('Server raw:', raw);
                        console.log('Client filtered:', this.results);
                    }
                } else {
                    if (typeof data.data === 'string' && data.data.includes('Security check')) {
                        await this.searchWithoutNonce();
                        return;
                    }

                    console.error('Search failed:', data.data);
                    this.results = [];
                    this.hasSearched = true;
                }
            } catch (err) {
                console.error('Search error:', err);
                this.results = [];
                this.hasSearched = true;
            } finally {
                this.isLoading = false;
            }
        },

        async searchWithoutNonce() {
            try {
                const { ajax_url } = window.searchAjax;
                const query = this.query.trim();

                const formData = new FormData();
                formData.append('action', 'search_products');
                formData.append('s', query);
                formData.append('per_page', '10');

                const res = await fetch(ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    const filtered = this.filterResultsByRelevance(data.data || [], query);
                    this.results = filtered.slice(0, 6);
                    this.hasSearched = true;
                } else {
                    console.error('Retry search failed:', data.data);
                    this.results = [];
                    this.hasSearched = true;
                }
            } catch (err) {
                console.error('Retry error:', err);
                this.results = [];
                this.hasSearched = true;
            }
        },

        performSearch() {
            if (this.query.trim() && !this.isGibberish(this.query)) {
                const baseUrl = window.searchAjax.home_url || window.location.origin;
                window.location.href = `${baseUrl}/?s=${encodeURIComponent(this.query.trim())}`;
            }
        },

        viewAllResults() {
            this.performSearch();
        }
    }));
});

{{-- resources/views/partials/single-product/product-links.blade.php --}}

@if (
    !empty($productLinks['has_links']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['companies', 'social_media_assets', 'newsletter'])
)
<section class="product-links-section py-20">
  <div class="container">
    <div class="product-links"
        x-data="{
          links: {{ Js::from($productLinks['links'] ?? []) }},
          totalCount: {{ $productLinks['total_count'] ?? 0 }},
          visibleLimit: {{ $productLinks['visible_limit'] ?? 6 }},
          showAll: false,
          textLimits: {},

          get visibleLinks() {
              return this.showAll ? this.links : this.links.slice(0, this.visibleLimit);
          },

          toggleShowAll() {
              this.showAll = !this.showAll;
          },

          initTextLimit(linkSlug, text) {
              const wordLimit = 20;
              const words = text.split(' ');
              this.textLimits[linkSlug] = {
                  full: text,
                  truncated: words.slice(0, wordLimit).join(' ') + (words.length > wordLimit ? '...' : ''),
                  isExpanded: false,
                  needsTruncation: words.length > wordLimit
              };
          },

          toggleText(linkSlug) {
              if (this.textLimits[linkSlug]) {
                  this.textLimits[linkSlug].isExpanded = !this.textLimits[linkSlug].isExpanded;
              }
          },

          getDisplayText(linkSlug) {
              const limit = this.textLimits[linkSlug];
              if (!limit) return '';
              return limit.isExpanded ? limit.full : limit.truncated;
          },

          openLink(url, target) {
              if (url && url !== '#') {
                  if (target === '_blank') {
                      window.open(url, '_blank');
                  } else {
                      window.location.href = url;
                  }
              }
          }
        }"
        x-init="
          if (links && Array.isArray(links)) {
              links.forEach(link => {
                  if (link && link.description && link.slug) {
                      initTextLimit(link.slug, link.description);
                  }
              });
          }
        ">

        {{-- Заголовок и описание --}}
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-blue-600 mb-4">
                Links
            </h2>

            {{-- Проверяем наличие описания перед выводом --}}
            @if(!empty($productLinks['description']))
                <div class="text-blue-600 text-lg max-w-4xl">
                    {{ $productLinks['description'] }}
                </div>
            @endif
        </div>

        {{-- Сетка ссылок (только если есть links) --}}
        @if(!empty($productLinks['links']) && is_array($productLinks['links']))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <template x-for="(link, index) in visibleLinks" :key="link.slug || index">
                    <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-all duration-300 cursor-pointer group relative"
                         :class="{ 'hover:border-blue-300': link.url && link.url !== '#' }"
                         @click="openLink(link.url, link.target)">

                        {{-- Заголовок ссылки --}}
                        <h3 class="text-xl font-semibold text-blue-600 mb-4 group-hover:text-blue-700 transition-colors"
                            x-text="link.title || 'Untitled Link'"></h3>

                        {{-- Описание с функцией show more/less --}}
                        <div class="text-gray-700 mb-6 flex-grow">
                            <div x-show="link.description">
                                <p class="leading-relaxed" x-text="getDisplayText(link.slug)"></p>

                                {{-- Кнопка show more/less --}}
                                <template x-if="textLimits[link.slug] && textLimits[link.slug].needsTruncation">
                                    <button @click.stop="toggleText(link.slug)"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 underline focus:outline-none">
                                        <span x-text="textLimits[link.slug].isExpanded ? 'Show less' : 'Show more'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Логотип/изображение --}}
                        <div class="mt-auto">
                            <template x-if="link.has_logo && link.logo && link.logo.url">
                                <div class="flex justify-center ">
                                    <img :src="link.logo.sizes?.medium || link.logo.url"
                                         :alt="link.logo.alt || link.title || 'Link logo'"
                                         class="max-h-55 max-w-full object-contain group-hover:scale-105 transition-transform duration-200 overflow-hidden rounded-4xl"
                                         loading="lazy">
                                </div>
                            </template>

                            {{-- Fallback если нет логотипа --}}
                            <template x-if="!link.has_logo || !link.logo || !link.logo.url">
                                <div class="flex justify-center">
                                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Индикатор внешней ссылки --}}
                        <template x-if="link.has_url && link.target === '_blank'">
                            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Кнопка показать больше/меньше ссылок --}}
            <template x-if="totalCount > visibleLimit">
                <div class="text-center">
                    <button
                        @click="toggleShowAll()"
                        class="text-blue-600 hover:text-blue-800 font-medium underline focus:outline-none transition-colors"
                    >
                        <span x-text="showAll ? 'Show less' : `Show ${totalCount - visibleLimit} more`"></span>
                        <span class="text-sm text-gray-500 ml-1">
                            (<span x-text="showAll ? totalCount : Math.min(visibleLimit, totalCount)"></span>/<span x-text="totalCount"></span>)
                        </span>
                    </button>
                </div>
            </template>
        @else
            {{-- Fallback если нет ссылок --}}
            <div class="text-center py-8 text-gray-500">
                <p>No links available</p>
            </div>
        @endif
    </div>
  </div>
</section>
@endif

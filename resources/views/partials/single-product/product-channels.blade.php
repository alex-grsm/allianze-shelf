{{-- resources/views/partials/single-product/product-channels.blade.php --}}

@if (
    !empty($productChannels['has_channels']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['companies', 'social_media_assets'])
)
<section class="product-channels-section py-20">
  <div class="container">
    <div class="product-channels "
        x-data="{
          channels: {{ Js::from($productChannels['channels'] ?? []) }},
          totalCount: {{ $productChannels['total_count'] ?? 0 }},
          includedCount: {{ $productChannels['included_count'] ?? 0 }},
          visibleLimit: {{ $productChannels['visible_limit'] ?? 5 }},
          showAll: false,

          get visibleChannels() {
              return this.showAll ? this.channels : this.channels.slice(0, this.visibleLimit);
          },

          toggleShowAll() {
              this.showAll = !this.showAll;
          }
        }">

        {{-- Заголовки таблицы --}}
        <div class="grid grid-cols-[75%_24%] gap-4 pb-4 border-b-2 border-blue-600 mb-6">
            <div class="font-semibold text-lg text-blue-600">
                Channel/Touchpoint
            </div>
            <div class="font-semibold text-lg text-blue-600">
                Assets included
            </div>
        </div>

        {{-- Список каналов (только если есть channels) --}}
        @if(!empty($productChannels['channels']))
            <div class="space-y-2">
                <template x-for="(channel, index) in visibleChannels" :key="channel.slug || index">
                    <div class="grid grid-cols-[75%_24%] gap-4">

                        {{-- Название канала --}}
                        <div class="flex items-center bg-white px-3 py-2 rounded-lg">
                            <span class="text-blue-600 font-medium text-lg " x-text="channel.name || 'Unnamed Channel'"></span>
                        </div>

                        {{-- Статус включения --}}
                        <div class="flex justify-center items-center bg-white px-3 py-2 rounded-lg">
                            <template x-if="channel.included">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </template>
                            <template x-if="!channel.included">
                                {{-- Можно добавить иконку для "не включено" --}}
                                <span class="text-gray-400">—</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Кнопка показать больше/меньше --}}
            <template x-if="totalCount > visibleLimit">
                <div class="mt-4 text-center">
                    <button
                        @click="toggleShowAll()"
                        class="text-blue-600 hover:text-blue-800 font-medium underline focus:outline-none"
                    >
                        <span x-text="showAll ? 'Show less' : `Show ${totalCount - visibleLimit} more`"></span>
                    </button>
                </div>
            </template>
        @else
            {{-- Fallback если нет каналов --}}
            <div class="text-center py-8 text-gray-500">
                <p>No channels data available</p>
            </div>
        @endif
    </div>
  </div>
</section>
@endif

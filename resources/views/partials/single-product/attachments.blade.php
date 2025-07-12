{{-- resources/views/partials/single-product/attachments.blade.php --}}

@if (
    !empty($attachments['has_attachments']) &&
    isset($productAcfFields['product_type']) &&
    in_array($productAcfFields['product_type'], ['companies', 'social_media_assets', 'newsletter'])
)

<section class="attachments-section py-20 bg-white">
  <div class="container">
      {{-- Заголовок и описание --}}
      <div class="mb-12">
          <h2 class="text-3xl font-bold text-blue-600 mb-4">
              Attachments
          </h2>

          {{-- Проверяем наличие описания перед выводом --}}
          @if(!empty($attachments['description']))
              <div class="text-blue-600 text-lg max-w-4xl leading-relaxed">
                  {{ $attachments['description'] }}
              </div>
          @endif
      </div>

      {{-- Attachments Grid (только если есть attachments) --}}
      @if(!empty($attachments['attachments']) && is_array($attachments['attachments']))
          <div class="attachments-grid">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  @foreach($attachments['attachments'] as $index => $attachment)
                      {{-- Проверяем что attachment содержит необходимые данные --}}
                      @if(is_array($attachment) && !empty($attachment['file']) && !empty($attachment['file']['url']))
                          <div class="attachment-item relative">
                              <a href="{{ $attachment['file']['url'] }}"
                                 download="{{ $attachment['file']['filename'] ?? 'attachment-' . ($index + 1) }}"
                                 class="!no-underline attachment-button group flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-lg px-8 py-6 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                 target="_blank"
                                 rel="noopener noreferrer">

                                  {{-- Attachment Label --}}
                                  <span class="text-center">
                                      {{ $attachment['label'] ?? $attachment['file']['filename'] ?? 'Download Attachment' }}
                                  </span>

                                  {{-- Download Icon --}}
                                  <svg class="w-5 h-5 ml-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                       fill="none"
                                       stroke="currentColor"
                                       viewBox="0 0 24 24">
                                      <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                  </svg>

                                  {{-- Hover Effect --}}
                                  <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
                              </a>

                              {{-- File Info Tooltip (optional) --}}
                              @if(!empty($attachment['file']['filesize']) || !empty($attachment['file']['mime_type']))
                                  <div class="absolute -top-2 -right-2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10">
                                      @if(!empty($attachment['file']['filesize']))
                                          {{ $attachment['file']['filesize'] }}
                                      @endif
                                      @if(!empty($attachment['file']['mime_type']))
                                          <br>{{ strtoupper(pathinfo($attachment['file']['filename'] ?? '', PATHINFO_EXTENSION)) }}
                                      @endif
                                  </div>
                              @endif
                          </div>
                      @else
                          {{-- Fallback для поврежденных данных attachment --}}
                          <div class="attachment-item relative">
                              <div class="flex items-center justify-center w-full bg-gray-300 text-gray-600 font-medium text-lg px-8 py-6 rounded-2xl">
                                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.76 0L3.054 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                  </svg>
                                  <span>Invalid Attachment</span>
                              </div>
                          </div>
                      @endif
                  @endforeach
              </div>
          </div>
      @else
          {{-- Fallback если нет attachments --}}
          <div class="text-center py-12 text-gray-500">
              <div class="max-w-md mx-auto">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <p class="text-lg font-medium">No attachments available</p>
                  <p class="text-sm mt-1">Check back later for downloadable files</p>
              </div>
          </div>
      @endif
  </div>
</section>
@endif

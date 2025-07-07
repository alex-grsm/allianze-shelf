{{-- resources/views/partials/single-product/attachments.blade.php (упрощенная версия) --}}

@if($attachments && $attachments['has_attachments'])
<section class="attachments-section py-20 bg-white">
  <div class="container">
      {{-- Заголовок и описание --}}
      <div class="mb-12">
          <h2 class="text-3xl font-bold text-blue-600 mb-4">
              Attachments
          </h2>

          <div class="text-blue-600 text-lg max-w-4xl leading-relaxed">
              {{ $attachments['description'] }}
          </div>
      </div>

      {{-- Attachments Grid --}}
      <div class="attachments-grid">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($attachments['attachments'] as $attachment)
                  <div class="attachment-item relative">
                      <a href="{{ $attachment['file']['url'] }}"
                         download="{{ $attachment['file']['filename'] }}"
                         class="!no-underline attachment-button group flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-lg px-8 py-6 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">

                          {{-- Attachment Label --}}
                          <span>{{ $attachment['label'] }}</span>

                          {{-- Hover Effect --}}
                          <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
                      </a>
                  </div>
              @endforeach
          </div>
      </div>
  </div>
</section>
@endif

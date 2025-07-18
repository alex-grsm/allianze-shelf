{{-- Hero Section --}}
@if (!empty($heroData['enabled']) && !empty($heroData['slides']))
<section class="hero relative h-[calc(var(--full-vh))] min-h-[950px]">
  {{-- Hero Slider --}}
  <div class="hero-slider h-full bg-black">
    <div class="swiper-wrapper">

      @foreach ($heroData['slides'] as $index => $slide)
        <div class="swiper-slide relative">
          {{-- Background Image --}}
          <div class="absolute inset-0 z-0">
            @if (!empty($slide['background_image']['url']))
              <img
                src="{{ $slide['background_image']['url'] }}"
                alt="{{ $slide['background_image']['alt'] ?: $slide['title'] }}"
                class="w-full h-full object-cover"
                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
              >
            @else
              {{-- Placeholder if no image --}}
              <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                <div class="text-center text-gray-400">
                  <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <p class="text-sm">Add background image</p>
                </div>
              </div>
            @endif

            {{-- Static Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/0"></div>
          </div>

          {{-- Content Container --}}
          <div class="container mx-auto px-4 relative z-10 mt-40 xl:mt-51">
            <div class="hero-content-home max-w-2xl">

              {{-- Badge and Rating --}}
              @if (!empty($slide['badge']) || $slide['show_rating'])
                <div class="inline-flex items-center gap-2 mb-3">
                  @if (!empty($slide['badge']))
                    <div class="px-2 py-1 rounded-full border border-yellow-300 flex items-center justify-center min-h-7">
                      <span class="leading-3 text-white">{{ $slide['badge'] }}</span>
                    </div>
                  @endif

                  @if ($slide['show_rating'])
                    <div class="flex items-center">
                      <img
                        src="{{ Vite::asset('resources/images/demo/stars.webp') }}"
                        alt="Rating stars"
                        class="w-full h-full object-cover"
                      >
                    </div>
                  @endif
                </div>
              @endif

              {{-- Heading --}}
              @if (!empty($slide['title']))
                <div class="text-3xl lg:text-4xl text-white mb-6 tracking-tight leading-tight">
                  {{ $slide['title'] }}
                </div>
              @endif

              {{-- Description --}}
              @if (!empty($slide['description']))
                <p class="text-lg md:text-xl text-white/70 mb-6 leading-normal max-w-120">
                  {!! nl2br(e($slide['description'])) !!}
                </p>
              @endif

              {{-- CTA Button --}}
              @if (!empty($slide['cta_text']) && !empty($slide['cta_url']))
                <a
                  href="{{ $slide['cta_url'] }}"
                  class="!no-underline px-2 py-1 inline-flex items-center bg-white rounded-full font-bold min-h-8 min-w-28 justify-center hover:bg-gray-100 transition-colors duration-200"
                  @if (str_starts_with($slide['cta_url'], 'http'))
                    target="_blank" rel="noopener noreferrer"
                  @endif
                >
                  {{ $slide['cta_text'] }}
                </a>
              @endif
            </div>
          </div>
        </div>
      @endforeach

    </div>

    {{-- Pagination (only show if multiple slides) --}}
    @if ($heroData['has_multiple_slides'])
      <div class="!bottom-10 !left-0 !right-0 absolute z-30 flex justify-center">
        <div class="swiper-pagination"></div>
      </div>
    @endif
  </div>

  {{-- Gradient для затемнения --}}
  <div class="absolute bottom-0 left-0 right-0 z-1 pointer-events-none max-h-[calc(var(--full-vh)/2)]">
    <img
      src="{{ Vite::asset('resources/images/hero-gradient.webp') }}"
      alt="Hero gradient"
      class="w-full max-h-[calc(var(--full-vh)/2)]"
    >
  </div>
</section>

@else
{{-- Section hidden when hero is disabled or no slides configured --}}
@endif

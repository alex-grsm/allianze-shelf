{{-- Hero Section --}}
<section class="hero relative h-[calc(var(--full-vh))] min-h-[950px]">
  {{-- Hero Slider --}}
  <div class="hero-slider h-full bg-black">
    <div class="swiper-wrapper">

      {{-- Slide 1 - Always drive well --}}
      <div class="swiper-slide relative">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
          <img
            src="{{ Vite::asset('resources/images/demo/hero-bg-1.webp') }}"
            alt="Hero background 1"
            class="w-full h-full object-cover"
          >
          {{-- Overlay для затемнения --}}
          <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/60"></div>
        </div>

        {{-- Content Container --}}
        <div class="container mx-auto px-4 relative z-10 mt-40 xl:mt-51">
          <div class="hero-content-home max-w-2xl">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 mb-3">
              <div class="px-2 py-1 rounded-full border border-yellow-300 flex items-center justify-center min-h-7">
                <span class="leading-3 text-white">Campaign</span>
              </div>
              <div class="flex items-center">
                <img
                  src="{{ Vite::asset('resources/images/demo/stars.webp') }}"
                  alt="Stars"
                  class="w-full h-full object-cover"
                >
              </div>
            </div>

            {{-- Heading --}}
            <div class="text-3xl lg:text-4xl text-white mb-6 tracking-tight leading-tight">
              Always drive well
            </div>

            {{-- Description --}}
            <p class="text-lg md:text-xl text-white/70 mb-6 leading-normal">
              Germany's "Immer gut fahren" campaign<br>
              highlights safety and coverage for drivers.
            </p>

            {{-- CTA Button --}}
            <a
              href="#details"
              class="!no-underline px-2 py-1 inline-flex items-center bg-white rounded-full font-bold min-h-8 min-w-28 justify-center"
            >
              See details →
            </a>
          </div>
        </div>
      </div>

      {{-- Slide 2 - Innovation First --}}
      <div class="swiper-slide relative">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
          <img
            src="{{ Vite::asset('resources/images/demo/hero-bg-2.webp') }}"
            alt="Hero background 2"
            class="w-full h-full object-cover"
          >
          {{-- Overlay для затемнения --}}
          <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/0"></div>
        </div>

        {{-- Content Container --}}
        <div class="container mx-auto px-4 relative z-10 mt-40 xl:mt-51">
          <div class="hero-content-home max-w-2xl">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 mb-3">
              <div class="px-2 py-1 rounded-full border border-yellow-300 flex items-center justify-center min-h-7">
                <span class="leading-3 text-white">Innovation</span>
              </div>
              <div class="flex items-center">
                <img
                  src="{{ Vite::asset('resources/images/demo/stars.webp') }}"
                  alt="Stars"
                  class="w-full h-full object-cover"
                >
              </div>
            </div>

            {{-- Heading --}}
            <div class="text-3xl lg:text-4xl text-white mb-6 tracking-tight leading-tight">
              Innovation First
            </div>

            {{-- Description --}}
            <p class="text-lg md:text-xl text-white/70 mb-6 leading-normal">
              Leading the future with cutting-edge<br>
              technology and creative solutions.
            </p>

            {{-- CTA Button --}}
            <a
              href="#innovation"
              class="!no-underline px-2 py-1 inline-flex items-center bg-white rounded-full font-bold min-h-8 min-w-28 justify-center"
            >
              See details →
            </a>
          </div>
        </div>
      </div>

      {{-- Slide 3 - Global Impact --}}
      <div class="swiper-slide relative">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
          <img
            src="{{ Vite::asset('resources/images/demo/hero-bg-3.webp') }}"
            alt="Hero background 3"
            class="w-full h-full object-cover"
          >
          {{-- Overlay для затемнения --}}
          <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/0"></div>
        </div>

        {{-- Content Container --}}
        <div class="container mx-auto px-4 relative z-10 mt-40 xl:mt-51">
          <div class="hero-content-home max-w-2xl">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 mb-3">
              <div class="px-2 py-1 rounded-full border border-yellow-300 flex items-center justify-center min-h-7">
                <span class="leading-3 text-white">Global</span>
              </div>
              <div class="flex items-center">
                <img
                  src="{{ Vite::asset('resources/images/demo/stars.webp') }}"
                  alt="Stars"
                  class="w-full h-full object-cover"
                >
              </div>
            </div>

            {{-- Heading --}}
            <div class="text-3xl lg:text-4xl text-white mb-6 tracking-tight leading-tight">
              Global Impact
            </div>

            {{-- Description --}}
            <p class="text-lg md:text-xl text-white/70 mb-6 leading-normal">
              Making a difference worldwide through<br>
              sustainable marketing and brand strategies.
            </p>

            {{-- CTA Button --}}
            <a
              href="#impact"
              class="!no-underline px-2 py-1 inline-flex items-center bg-white rounded-full font-bold min-h-8 min-w-28 justify-center"
            >
              See details →
            </a>
          </div>
        </div>
      </div>

    </div>
    {{-- Pagination --}}
    {{-- <div class="!bottom-10 !left-0 !right-0 absolute z-30 flex justify-center">
      <div class="swiper-pagination "></div>
    </div> --}}
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

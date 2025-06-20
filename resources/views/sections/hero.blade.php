{{-- Hero Section --}}
<section class="h-[calc(var(--full-vh))] min-h-[800px] mt-19.5">

    <div class="">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0 min-h-[800px]">
            <img
              src="{{ Vite::asset('resources/images/demo/cat-bg-1.webp') }}"
              alt="Hero background 1"
              class="w-full !h-full object-cover min-h-[800px]"
            >
            {{-- Overlay для затемнения --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/65 to-black/10"></div>
        </div>

        {{-- Content Container --}}
        <div class="absolute inset-0 z-0">
            <div class="container mx-auto px-4 relative z-10 mt-40 xl:mt-74">
                <div class="hero-content max-w-3xl">

                    {{-- Heading --}}
                    <div class="text-5xl lg:text-7xl text-white tracking-tight leading-3">
                        Property & Casualty
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>

{{-- resources/views/partials/categories/category-hero.blade.php --}}
@php
  $category = get_queried_object();
  $category_name = ($category instanceof WP_Term && isset($category->name)) ? $category->name : 'Category not found';
  $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
  $thumbnail_url = wp_get_attachment_url($thumbnail_id);
@endphp


<section class="h-[calc(var(--full-vh))] min-h-[800px] mt-19.5">

    <div class="">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0 min-h-[800px]">
            @if ($thumbnail_url)
              <img
                src="{{ $thumbnail_url }}"
                alt="{{ $category->name }}"
                class="w-full !h-full object-cover min-h-[800px]"
              >
            @endif
            {{-- <img
              src="{{ Vite::asset('resources/images/demo/cat-bg-1.webp') }}"
              alt="Hero background 1"
              class="w-full !h-full object-cover min-h-[800px]"
            > --}}
            {{-- Overlay для затемнения --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/65 to-black/10"></div>
        </div>

        {{-- Content Container --}}
        <div class="absolute inset-0 z-0">
            <div class="container mx-auto px-4 relative z-10 mt-40 xl:mt-74">
                <div class="hero-content max-w-3xl">

                    {{-- Heading --}}
                    <div class="text-5xl lg:text-7xl text-white tracking-tight leading-3">
                        {{ $category_name }}
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-purple-600">Все доступные переменные:</h4>
      @dump($thumbnail_id)
      @dump($thumbnail_url)
    </div> --}}

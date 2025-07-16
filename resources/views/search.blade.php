{{-- resources/views/search.blade.php --}}
@extends('layouts.app')

@section('content')
  {{-- Hero Section с поиском --}}
  <section class="search-hero mt-19.5 bg-[linear-gradient(2deg,#666_0%,#000_35%)]">
      <div class="container">
          <div class="py-20">
              {{-- Заголовок с поисковым запросом --}}
              <div class="mb-8">
                  <h1 class="text-4xl lg:text-5xl text-white tracking-tight leading-tight mb-4">
                      @if(get_search_query())
                          Search Results for "<span class="text-yellow-300">{{ get_search_query() }}</span>"
                      @else
                          Search Products
                      @endif
                  </h1>

                  @if(have_posts())
                      @php
                          global $wp_query;
                          $total_results = $wp_query->post_count;
                      @endphp
                      <p class="text-lg text-white/70">
                          Found {{ number_format($total_results) }}
                          @if($total_results === 1)
                              result
                          @else
                              results
                          @endif
                      </p>
                  @else
                      <p class="text-lg text-white/70">
                          No results found. Try different keywords.
                      </p>
                  @endif
              </div>

              {{-- Поисковая форма --}}
              <div class="search-form-wrapper max-w-2xl">
                  <form role="search" method="get" action="{{ home_url('/') }}" class="relative">
                      <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                              <x-svg-icon name="search" class="h-5 w-5 text-gray-400" />
                          </div>
                          <input
                              type="search"
                              name="s"
                              value="{{ get_search_query() }}"
                              placeholder="Search..."
                              class="block w-full pl-12 pr-20 py-4 text-lg border-0 bg-white rounded-xl text-blue-600 focus:ring-2 focus:ring-blue-500 transition-all"
                              autocomplete="off"
                          >
                          <div class="absolute inset-y-0 right-0 flex items-center">
                              <button
                                  type="submit"
                                  class="bg-blue-700 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg mr-2 transition-colors"
                              >
                                  Search
                              </button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </section>

<div class="container py-12">
    @if (have_posts())
        <div class="products-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-1 gap-6 mb-8">
            @while(have_posts())
                @php
                    the_post();
                @endphp

                {{-- @include('partials.product-card') --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-2">
                        <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
                    </h2>
                    <p class="text-gray-600">{{ get_the_excerpt() }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ get_the_date() }}</p>
                </div>
            @endwhile
        </div>
    @else
        <p class="text-2xl">No results found.</p>
    @endif
</div>
@endsection

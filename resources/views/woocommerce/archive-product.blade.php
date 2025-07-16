{{--
  Template for product category pages
--}}

{{-- @extends('layouts.app')

@section('content')
    @include('sections.hero')
    @include('sections.car')
    @include('sections.cat-1')
    @include('sections.cat-2')
    @include('sections.related')
@endsection --}}



@extends('layouts.app')

@section('content')
    @include('partials.categories.category-hero')
    @include('partials.categories.category-products')
@endsection



    {{-- <div class="mb-4">
      <h4 class="font-semibold text-blue-600">ProductSummary данные:</h4>
      @dump($productSummary)
    </div> --}}

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-green-600">ProductAcfFields данные:</h4>
      @dump($productAcfFields)
    </div> --}}

    {{-- <div class="mb-4">
      <h4 class="font-semibold text-purple-600">Все доступные переменные:</h4>
      @dump(get_defined_vars())
    </div> --}}

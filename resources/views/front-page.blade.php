@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('sections.hero')
    @include('sections.campaings')
    @include('sections.channels')
    @include('sections.products')
    @include('sections.sales')
    @include('sections.sponsoring')
    @include('sections.concepts-reports')
    @include('sections.support')
    {{-- @include('partials.page-header') --}}
    {{-- @includeFirst(['partials.content-page', 'partials.content']) --}}
  @endwhile
@endsection

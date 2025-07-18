@extends('layouts.app')

@section('content')
    {{-- @include('sections.hero-home') --}}
    @include('partials.hero-home')
    @include('partials.categories.home-categories-products')
    {{-- @include('sections.campaings') --}}
    {{-- @include('sections.channels') --}}
    {{-- @include('sections.products') --}}
    {{-- @include('sections.sales') --}}
    {{-- @include('sections.sponsoring') --}}
    @include('sections.concepts-reports')
    @include('sections.support')
@endsection

{{-- resources/views/search.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-12">
    <h1 class="text-3xl font-bold mb-8">Search Results</h1>

    @if (have_posts())
        <div class="grid gap-4">
            @while(have_posts())
                @php
                    the_post();
                @endphp
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
        <p>No results found.</p>
    @endif
</div>
@endsection

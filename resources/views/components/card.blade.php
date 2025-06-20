<div class="w-full bg-white rounded-2xl overflow-hidden flex flex-col">
    <a href="{{ get_permalink(73) }}" class="!no-underline">
        <div class="relative">
            <img src="{{ Vite::asset($image) }}" alt="{{ $title }}" class="w-full h-87.5 object-cover rounded-2xl"
                loading="lazy">

            @if ($label)
                <span
                    class="absolute top-3 left-3 bg-[#f62459] text-white font-semibold px-2 py-1 rounded-2xl leading-3">
                    {{ $label }}
                </span>
            @endif

            <span class="absolute bottom-3 left-3 rounded-full overflow-hidden">
                <img src="{{ flag_url($flag ?? '') }}" alt="Flag"
                    class="size-6.5 object-cover">
            </span>
        </div>

        <div class="pt-2.5 pb-4 px-3 flex flex-col flex-1">
            <h3 class="text-xl font-bold mb-1.5">{{ $title }}</h3>

            <div class="concept-meta space-y-1.5 mb-3.5 text-sm">
                <p><span class="font-bold">Target:</span> {{ $target }}</p>
                <p>
                    <span class="font-bold">Year:</span> {{ $year }} |
                    <span class="font-bold">Buyout:</span> {{ $buyout }}
                </p>
            </div>

            <div class="mt-auto flex justify-between items-center">
                <span
                    class="!no-underline concept-tag inline-flex items-center px-3 py-1 rounded-full text-sm border border-purple-600">
                    {{ $tag }}
                </span>

                <div class="">
                    <img src="{{ Vite::asset('resources/images/demo/stars.webp') }}" alt="Stars"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </a>
</div>

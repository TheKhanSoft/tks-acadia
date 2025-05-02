<div {{ $attributes->merge(['class' => '']) }}>
    <a href="/" class="flex items-center">
        <img src="{{ asset('images/logos/tks-logo01.jpeg') }}" class="h-8 mr-3" alt="{{ config('app.name') }} Logo" />
        <span class="self-center text-2xl font-semibold whitespace-nowrap">{{ config('app.name') }}</span>
    </a>
</div>

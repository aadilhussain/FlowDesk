@props(['title', 'meta' => null, 'id' => null])

<section @if ($id) id="{{ $id }}" @endif {{ $attributes->merge(['class' => 'section']) }}>
    <div class="section-head">
        <div>
            <h2>{{ $title }}</h2>
            @if ($meta)
                <span>{{ $meta }}</span>
            @endif
        </div>
    </div>

    {{ $slot }}
</section>

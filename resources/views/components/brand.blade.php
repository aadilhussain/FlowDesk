@props(['caption' => null])

<a href="{{ route('home') }}" class="brand" aria-label="FlowDesk home">
    <span class="mark">F</span>
    <span>
        <strong>FlowDesk</strong>
        @if ($caption)
            <span>{{ $caption }}</span>
        @endif
    </span>
</a>

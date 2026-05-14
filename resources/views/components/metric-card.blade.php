@props(['label', 'value', 'note'])

<div class="metric">
    <span>{{ $label }}</span>
    <strong>{{ $value }}</strong>
    <small>{{ $note }}</small>
</div>

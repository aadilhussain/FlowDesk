@props(['title', 'description', 'tag', 'meta'])

<article class="task">
    <strong>{{ $title }}</strong>
    <p>{{ $description }}</p>
    <div class="task-meta">
        <span class="tag">{{ $tag }}</span>
        <span>{{ $meta }}</span>
    </div>
</article>

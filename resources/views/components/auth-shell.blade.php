@props(['title', 'description'])

<main class="auth-shell">
    <section class="auth-intro">
        <x-brand />
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>

    <section class="auth-panel">
        {{ $slot }}
    </section>
</main>

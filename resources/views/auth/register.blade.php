<x-layouts.app title="Create account | FlowDesk" body-class="auth-page">
    <x-auth-shell
        title="Create your tenant workspace."
        description="Registration creates a user, Sanctum token, and default workspace so teams can start managing tasks immediately."
    >
        <h2>Start FlowDesk</h2>
        <p>Create the first account for your workspace.</p>

        <form data-auth-form data-auth-action="register">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" autocomplete="name" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="email" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8">

            <button type="submit">Create account</button>
        </form>

        <a class="secondary" href="{{ route('login') }}">Already have an account?</a>
        <a class="secondary" href="{{ route('home') }}">Back to overview</a>

        <div data-auth-message class="message"></div>
    </x-auth-shell>
</x-layouts.app>

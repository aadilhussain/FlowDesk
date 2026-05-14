<x-layouts.app title="Sign in | FlowDesk" body-class="auth-page">
    <x-auth-shell
        title="Sign in to your workflow command center."
        description="Use your API account to manage workspaces, tenant-scoped tasks, and operational workflows."
    >
        <h2>Welcome back</h2>
        <p>Enter your FlowDesk credentials.</p>

        <form data-auth-form data-auth-action="login">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="email" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Sign in</button>
        </form>

        <a class="secondary" href="{{ route('register') }}">Create a workspace account</a>
        <a class="secondary" href="{{ route('home') }}">Back to overview</a>

        <div data-auth-message class="message"></div>
    </x-auth-shell>
</x-layouts.app>

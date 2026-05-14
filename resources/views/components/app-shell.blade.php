<div class="app">
    <aside class="sidebar" aria-label="FlowDesk navigation">
        <x-brand caption="Workflow operations" />

        <div class="workspace">
            <span>Current workspace</span>
            <strong>Acme Operations</strong>
        </div>

        <nav class="nav">
            <a href="{{ route('home') }}" class="active"><span class="dot"></span>Overview</a>
            <a href="#api-surface"><span class="dot"></span>API Surface</a>
            <a href="#kanban"><span class="dot"></span>Kanban</a>
            <a href="#workflow"><span class="dot"></span>Automation</a>
            <a href="#roadmap"><span class="dot"></span>Roadmap</a>
        </nav>

        <div class="sidebar-footer">
            API-first SaaS foundation with Sanctum, tenant policies, resources, and MySQL migrations ready.
        </div>
    </aside>

    <main class="main">
        {{ $slot }}
    </main>
</div>

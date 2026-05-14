<x-layouts.app title="FlowDesk | Workflow Operations">
    <x-app-shell>
        <header class="topbar">
            <div class="title">
                <h1>Workspace command center</h1>
                <p>Track tenant work, task ownership, automation readiness, and API health from one operational view.</p>
            </div>
            <div class="actions">
                <a class="button" href="{{ route('login') }}">Sign in</a>
                <a class="button primary" href="{{ route('register') }}">Create account</a>
            </div>
        </header>

        <section class="health" aria-label="Workspace health">
            <x-metric-card label="Active tasks" value="24" note="8 moving today" />
            <x-metric-card label="Workspaces" value="6" note="Tenant scoped" />
            <x-metric-card label="Automations" value="12" note="Queue ready" />
            <x-metric-card label="API status" value="v1" note="Protected routes" />
        </section>

        <div class="layout">
            <x-section
                id="kanban"
                title="Delivery board"
                meta="Realistic task workflow mapped to pending, in progress, and completed API states."
                aria-label="Kanban board"
            >
                <div class="kanban">
                    <div class="lane">
                        <div class="lane-title">Pending <span class="count">3</span></div>
                        <x-task-card
                            title="Invite workspace members"
                            description="Add tenant membership endpoints and prepare role assignment rules."
                            tag="SaaS"
                            meta="Due today"
                        />
                        <x-task-card
                            title="Define workflow triggers"
                            description="Model task-created, status-changed, and overdue triggers for automation."
                            tag="Automation"
                            meta="2d"
                        />
                        <x-task-card
                            title="Deployment checklist"
                            description="Document environment variables, queue worker, cache, and scheduler setup."
                            tag="AWS"
                            meta="4d"
                        />
                    </div>

                    <div class="lane">
                        <div class="lane-title">In progress <span class="count">4</span></div>
                        <x-task-card
                            title="Tenant-scoped task API"
                            description="Route tasks through workspace membership and policy authorization."
                            tag="API"
                            meta="Owner"
                        />
                        <x-task-card
                            title="Kanban Vue shell"
                            description="Plan drag states, API hydration, optimistic updates, and empty lanes."
                            tag="Vue"
                            meta="Next"
                        />
                        <x-task-card
                            title="Role permission matrix"
                            description="Map owner, admin, and member capabilities across workspaces and tasks."
                            tag="Access"
                            meta="Review"
                        />
                    </div>

                    <div class="lane">
                        <div class="lane-title">Completed <span class="count">5</span></div>
                        <x-task-card
                            title="Sanctum authentication"
                            description="Register, login, token generation, and protected API route foundation."
                            tag="Auth"
                            meta="Done"
                        />
                        <x-task-card
                            title="Task CRUD module"
                            description="Model, migration, requests, resource, policy, controller, and tests."
                            tag="Tasks"
                            meta="Done"
                        />
                        <x-task-card
                            title="MySQL migrations"
                            description="Production database schema applied and verified locally."
                            tag="DB"
                            meta="Done"
                        />
                    </div>
                </div>
            </x-section>

            <aside class="side-stack">
                <x-section id="workflow" title="Workflow pipeline" meta="Live plan">
                    <div class="panel-body workflow">
                        <div class="step">
                            <div class="step-index">1</div>
                            <div><strong>Task created</strong><span>Validated by Form Request</span></div>
                            <div class="status">ready</div>
                        </div>
                        <div class="step">
                            <div class="step-index">2</div>
                            <div><strong>Policy checked</strong><span>Workspace membership enforced</span></div>
                            <div class="status">ready</div>
                        </div>
                        <div class="step">
                            <div class="step-index">3</div>
                            <div><strong>Notification queued</strong><span>Next backend milestone</span></div>
                            <div class="muted">next</div>
                        </div>
                    </div>
                </x-section>

                <x-section id="api-surface" title="API surface" meta="REST v1">
                    <div class="panel-body">
                        <ul class="api-list">
                            <li><code>POST /api/v1/register</code><span class="ready">live</span></li>
                            <li><code>GET /api/v1/workspaces</code><span class="ready">live</span></li>
                            <li><code>POST /api/v1/workspaces</code><span class="ready">live</span></li>
                            <li><code>GET /api/v1/workspaces/{id}/tasks</code><span class="ready">live</span></li>
                            <li><code>PATCH /api/v1/workspaces/{id}/tasks/{id}</code><span class="ready">live</span></li>
                        </ul>
                    </div>
                </x-section>

                <x-section id="roadmap" title="Production roadmap" meta="Next up">
                    <div class="panel-body roadmap">
                        <div><strong>Role permissions</strong><span class="muted">backend</span></div>
                        <div><strong>Queue notifications</strong><span class="muted">backend</span></div>
                        <div><strong>Vue Kanban</strong><span class="muted">frontend</span></div>
                        <div><strong>Docker and AWS</strong><span class="muted">deploy</span></div>
                    </div>
                </x-section>
            </aside>
        </div>
    </x-app-shell>
</x-layouts.app>

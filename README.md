# FlowDesk

FlowDesk is a multi-tenant SaaS workflow and task management platform built with Laravel and Vue.js. The goal is to give teams a clean operational workspace where they can manage tasks, organize work by tenant/workspace, and later automate repeated workflow steps through queues, notifications, and rules.

The project is currently focused on building a strong Laravel API foundation before expanding the Vue Kanban experience and deployment layer.

## Tech Stack

- Laravel 13
- Vue.js
- Laravel Sanctum authentication
- MySQL
- REST APIs
- Blade views and reusable Blade components
- Vite asset pipeline
- PHPUnit feature tests

## What FlowDesk Does

FlowDesk is designed around workspaces. A workspace represents a tenant, company, team, or client account. Users belong to workspaces, and tasks are scoped to those workspaces so each tenant only sees and manages its own data.

At this stage, the app includes:

- Account registration and login APIs
- Sanctum token generation
- Default workspace creation during registration
- Workspace CRUD APIs
- Workspace membership foundation
- Tenant-scoped task CRUD APIs
- Task ownership policies
- API resources for consistent JSON responses
- Form Requests for validation
- A production-style landing page
- Browser login and register pages for local testing

## Features Implemented

### Authentication

- Register API
- Login API
- Sanctum token system
- Request validation for auth payloads
- Protected API routes with `auth:sanctum`

### Multi-Tenant Workspace Foundation

- `Workspace` model
- `workspaces` table
- `user_workspace` membership pivot table
- Workspace roles: `owner`, `admin`, `member`
- Workspace policies so only members can view a workspace
- Owner/admin authorization for workspace updates

### Task Management

- `Task` model
- Task migration
- Task status enum:
  - `pending`
  - `in_progress`
  - `completed`
- Tenant-scoped task routes
- Task ownership checks
- Form Request validation
- API Resource responses
- Feature tests for CRUD, validation, and authorization

### Frontend Foundation

- FlowDesk landing page replacing the default Laravel welcome screen
- Reusable Blade layout and components
- Login and register pages
- Shared CSS in `resources/css/app.css`
- Shared auth JavaScript in `resources/js/app.js`
- Public fallback CSS/JS assets for local use before running a Vite build

## API Overview

### Auth

```http
POST /api/v1/register
POST /api/v1/login
```

### Workspaces

```http
GET    /api/v1/workspaces
POST   /api/v1/workspaces
GET    /api/v1/workspaces/{workspace}
PATCH  /api/v1/workspaces/{workspace}
DELETE /api/v1/workspaces/{workspace}
```

### Tasks

```http
GET    /api/v1/workspaces/{workspace}/tasks
POST   /api/v1/workspaces/{workspace}/tasks
GET    /api/v1/workspaces/{workspace}/tasks/{task}
PATCH  /api/v1/workspaces/{workspace}/tasks/{task}
DELETE /api/v1/workspaces/{workspace}/tasks/{task}
```

Protected endpoints require a Sanctum bearer token:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

## Local Setup

Clone the repository and install PHP dependencies:

```bash
composer install
```

Create your environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Configure your MySQL database in `.env`, then run migrations:

```bash
php artisan migrate
```

Start the local Laravel server:

```bash
php artisan serve
```

The app will be available at:

```text
http://127.0.0.1:8000
```

## Frontend Assets

For development, install Node dependencies and run Vite:

```bash
npm install
npm run dev
```

For production assets:

```bash
npm run build
```

The app also includes fallback files in `public/css/flowdesk.css` and `public/js/flowdesk.js` so the basic browser views still work locally before the Vite build is available.

## Testing

Run the full test suite:

```bash
php artisan test
```

Current test coverage includes:

- Authentication registration/login behavior
- Workspace creation and authorization
- Tenant-scoped task CRUD
- Task validation
- Cross-user and cross-workspace access protection

## Current Status

FlowDesk now has the backend foundation for a multi-tenant SaaS task system. The next major milestone is to build a richer role and permission layer, then connect the backend to a Vue Kanban board.

## Roadmap

- Role and permission system
- Queue and notification system
- Workflow automation engine
- Vue-powered Kanban board
- Workspace invitation flow
- Docker setup
- AWS deployment configuration
- Production monitoring and logging

## Development Notes

The app follows Laravel API best practices where possible:

- Controllers stay focused on request flow
- Form Requests handle validation
- Policies handle authorization
- Resources shape API responses
- Enums define stable domain states
- Feature tests protect important user flows

FlowDesk is still early, but the foundation is intentionally structured so the product can grow without turning the codebase into a tangle.

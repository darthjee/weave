# Architecture

## Overview

Weave is structured as two independent applications — a Django REST backend and a React/Vite frontend — served together through the Tent proxy. The backend owns all data and business logic; the frontend is a purely static single-page application that consumes the backend API. Tent is the single entry point: it routes requests to the correct upstream and caches backend responses.

## Tent Proxy (`weave_proxy`)

Tent ([GitHub](https://github.com/darthjee/tent), [Docker Hub](https://hub.docker.com/r/darthjee/tent)) is a PHP-based reverse proxy and static file server. It listens on port 3000 and is the only service exposed to end users.

Routing is configured via PHP files in `docker_volumes/proxy_configuration/`:

- `configure.php` — entry point that loads the rule files.
- `rules/backend.php` — routes `GET /api/*` to the Django backend (`weave_app:8080`) using Tent's `default_proxy` handler (automatic `Host` header rewriting and file-based caching), with an explicit `Host: localhost` override middleware to satisfy Django's dev `ALLOWED_HOSTS`.
- `rules/frontend.php` — routes frontend requests. Behaviour depends on the `FRONTEND_DEV_MODE` environment variable:

### Development mode (`FRONTEND_DEV_MODE=true`)

Tent proxies live frontend requests to the Vite dev server (`weave_fe:8080`):

| Path pattern | Handler |
|---|---|
| `GET /` | Vite dev server |
| `GET /assets/js/*` | Vite dev server |
| `GET /assets/css/*` | Vite dev server |
| `GET /@vite/*` | Vite dev server (HMR) |
| `GET /node_modules/*` | Vite dev server |
| `GET /@react-refresh` | Vite dev server (HMR) |
| `GET /assets/images/*` | Served statically from `/var/www/html/static` |

### Production / static mode (`FRONTEND_DEV_MODE=false` or unset)

Tent serves all frontend assets from its static folder:

| Path pattern | Handler |
|---|---|
| `GET /` | Serves `index.html` statically |
| `GET /index.html` | Served statically |
| `GET /assets/*` | Served statically |

The static root (`/var/www/html/static` inside the container) is populated from two sources:
- `proxy/static/` — committed static assets (images, etc.).
- `docker_volumes/static/` — Vite build output (JS, CSS, `index.html`); this is the shared volume described below.

### Production Proxy Configuration (`prod_proxy_config/`)

Unlike `docker_volumes/proxy_configuration/` (used for local development), `prod_proxy_config/` is the Tent configuration deployed to production. It mirrors the same structure with two key differences:

- There is no dev-mode branch — the frontend is always served statically.
- The backend host is extracted into `prod_proxy_config/hosts.php`, which is included by `prod_proxy_config/configure.php`. `hosts.php` is gitignored (it holds the real deployment-specific host); `prod_proxy_config/hosts.php.sample` is committed as a template, pointing to `localhost:3030`.
- `rules/backend.php` uses Tent's `default_proxy` handler (instead of the low-level `proxy` handler used in dev) — it automatically rewrites the `Host` header to the upstream host and enables file-based response caching, so no explicit middlewares are needed in the rule definition.

#### Deployment

The `upload_proxy_files` CI job (`.circleci/config.yml`) uploads the local `prod_proxy_config/` into the remote release's `configuration/` folder, then copies only the previous `hosts.php` from the old configuration into the new one — preserving the deployment-specific host value across releases without carrying over any other stale configuration.

## Shared Volume: Frontend Build Output

`docker_volumes/static/` is mounted into both `weave_fe` and `weave_proxy`:

- In `weave_fe`: mounted as `/home/node/app/dist` — this is Vite's `outDir`, so `npm run build` writes here.
- In `weave_proxy`: sub-paths are mounted into `/var/www/html/static/` (JS, CSS, `index.html`).

This means a frontend build is immediately available to Tent without any copy step.

## Backend (`backend/`)

All Django source code lives under `backend/`.

### `weave/`

Django project package: `settings.py`, root `urls.py`, `wsgi.py`, and `asgi.py`. Entry point for the Django application.

### `curriculum/`

The core Django app. Contains the domain models, REST views, and serializers for CV data.

- `models/` — Domain models (e.g. `person.py`).
- `views/` — Class-based API views (e.g. `person_retrieve_view.py`, `default_person_view.py`).
- `serializers/` — DRF serializers (e.g. `person_serializer.py`).
- `migrations/` — Django database migrations.
- `tests/` — Unit and integration tests for the curriculum app.
- `admin.py` — Django Admin registrations.
- `urls.py` — URL routing for the curriculum app.

### `accounts/`

Django app handling user/account concerns. Currently minimal (migrations only).

### `assets/`

Static asset sources for the backend (served via Django's static files pipeline).

### `templates/`

Django HTML templates.

### `bin/`

Backend utility scripts.

### `mysql/`

MySQL-related configuration or seed files used in development.

## Frontend (`frontend/`)

All React source code lives under `frontend/`.

### `assets/`

Static asset sources — CSS (`assets/css/`) and JavaScript (`assets/js/`) used by the frontend.

### `spec/`

Frontend test files. `clients/` contains API client specs.

### `dist/`

Vite build output directory. Mounted to `docker_volumes/static/` so Tent can serve the built files directly.

### `index.html`

SPA entry point consumed by Vite.

### `vite.config.js`

Vite bundler configuration.

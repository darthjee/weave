# Architecture

## Overview

Weave is structured as two independent applications — a Django REST backend and a React/Vite frontend — served together through the Tent proxy. The backend owns all data and business logic; the frontend is a purely static single-page application that consumes the backend API. Tent is the single entry point: it routes requests to the correct upstream and caches backend responses.

## Tent Proxy (`weave_proxy`)

Tent ([GitHub](https://github.com/darthjee/tent), [Docker Hub](https://hub.docker.com/r/darthjee/tent)) is a PHP-based reverse proxy and static file server. It listens on port 3000 and is the only service exposed to end users.

Routing is configured via PHP files in `docker_volumes/proxy_configuration/`:

- `configure.php` — entry point that loads the rule files.
- `rules/backend.php` — routes `GET /api/*` to the Django backend (`weave_app:8080`), with file-based caching for 2xx responses.
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

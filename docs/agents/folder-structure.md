# Folder Structure

## Project Root

| Directory / File | Description |
|-----------------|-------------|
| `backend/` | Django application — models, views, serializers, settings, and entry point. |
| `frontend/` | React 19 + Vite application — UI components, assets, specs, and build output. |
| `proxy/` | Tent proxy configuration sources and static file output consumed by the proxy. |
| `dockerfiles/` | Docker build definitions for each service (backend, frontend, proxy, CI, etc.). |
| `docker_volumes/` | Docker persistent data (mostly gitignored); mounted into containers at runtime. |
| `docs/` | Project documentation, including agent instructions and plans. |
| `scripts/` | Utility shell scripts for deployment and production access. |
| `docker-compose.yml` | Docker Compose service definitions for local development. |
| `Makefile` | Convenience targets for common development and deployment tasks. |
| `phpcs.xml` | PHP CodeSniffer configuration (used by the Tent proxy layer). |

## `docker_volumes/`

| Subdirectory | Description |
|--------------|-------------|
| `mysql_data/` | MySQL database files persisted across container restarts. |
| `node_modules/` | Frontend Node.js dependencies mounted into the Vite container. |
| `proxy_configuration/` | Runtime configuration files for the Tent proxy. |
| `static/` | Static files built by the frontend and Django collectstatic, served by the proxy. |
| `vendor/` | PHP vendor dependencies for the proxy/Tent layer. |

## `dockerfiles/`

| Subdirectory | Description |
|--------------|-------------|
| `weave/` | Production image for the Django backend. |
| `weave-base/` | Base image for the Django backend (shared deps). |
| `production_weave/` | Production-optimised backend image. |
| `production_weave-base/` | Base for the production backend image. |
| `vite_weave/` | Image for running the Vite dev server / build. |
| `vite_weave-base/` | Base image for the Vite container. |
| `proxy_weave/` | Image for the Tent proxy. |
| `circleci_weave-base/` | Base image used in CI pipelines. |

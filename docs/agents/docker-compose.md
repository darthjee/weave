# Docker Compose Structure

## Overview

The `docker-compose.yml` defines all services for local development and production. Services are grouped by purpose: infrastructure, base images, development containers, and production containers.

## Infrastructure

| Service | Image | Port | Description |
|---|---|---|---|
| `weave_mysql` | `mysql:9.3.0` | `3306` | MySQL database for development. Data persisted in `docker_volumes/mysql_data/`. |
| `weave_prod_mysql` | `mysql:9.3.0` | — | MySQL database for production (uses `docker_volumes/mysql_data/` and `.env.prod`). |
| `weave_phpmyadmin` | `phpmyadmin/phpmyadmin` | `3050` | phpMyAdmin UI for the development database. |
| `weave_prod_phpmyadmin` | `phpmyadmin/phpmyadmin` | `3050` | phpMyAdmin UI for the production database. |
| `weave_httpbin` | `kennethreitz/httpbin` | `3060` | HTTP request inspection tool, useful for debugging proxy routing. |

## Base Image Definitions

These services exist only to build shared images — they do not run long-lived processes.

| Service | Image | Description |
|---|---|---|
| `base` | `darthjee/weave` | YAML anchor (`&base`) with the shared Django config: mounts `./backend`, links to MySQL, loads `.env`. |
| `base_build` | `darthjee/weave` | Triggers a build of the `weave` Docker image (`dockerfiles/Dockerfile.weave`). |
| `base_prod` | `darthjee/production_weave` | YAML anchor (`&base_prod`) for production Django config: links to `weave_prod_mysql`, loads `.env.prod`. |
| `base_prod_build` | `darthjee/production_weave` | Triggers a build of the production Django image (`dockerfiles/Dockerfile.production_weave`). |

## Development Containers

| Service | Port | Description |
|---|---|---|
| `weave_app` | `3030` | Django development server. Depends on `base_build` and `weave_mysql`. |
| `weave_fe` | `3010` | Vite dev server. Mounts `./frontend`, `docker_volumes/node_modules`, and `docker_volumes/static` (as `dist/`). |
| `weave_proxy` | `3000` | Tent proxy — main entry point. Routes to `weave_app` (backend) and `weave_fe` (frontend). See [Architecture](architecture.md) for routing details. |
| `weave_tests` | — | One-off container for running the backend test suite (`poetry run pytest`). |
| `weave_root` | — | Same as `weave_app` but runs as root with a bash shell — useful for debugging inside the container. |

### Shared volumes in development

| Volume path (host) | Mounted into | Purpose |
|---|---|---|
| `./backend` | `weave_app:/home/app/app` | Live-reload backend code without rebuilding the image. |
| `./frontend` | `weave_fe:/home/node/app` | Live-reload frontend code. |
| `docker_volumes/node_modules` | `weave_fe:/home/node/app/node_modules` | Node dependencies persisted across container restarts. |
| `docker_volumes/static` | `weave_fe:/home/node/app/dist` and `weave_proxy:/var/www/html/static/...` | Shared build output: Vite writes here, Tent serves from here. |
| `docker_volumes/proxy_configuration` | `weave_proxy:/var/www/html/configuration/` | Tent routing rules, editable without rebuilding the proxy image. |
| `proxy/static/` | `weave_proxy:/var/www/html/static/` | Committed static assets (images, etc.) served directly by Tent. |

## Production Containers

| Service | Port | Description |
|---|---|---|
| `weave_prod_app` | `3030` | Production Django server. Uses the production image, links to `weave_prod_mysql`, sets `STAGE=production`. |

## Port Map

| Port | Service |
|---|---|
| `3000` | Tent proxy (main entry point) |
| `3010` | Vite dev server |
| `3030` | Django backend |
| `3050` | phpMyAdmin |
| `3060` | httpbin |

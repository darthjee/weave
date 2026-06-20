# Plan: Add cache warm up

Issue: [109-add-cache-warm-up.md](../../issues/109-add-cache-warm-up.md)

## Overview

Add an automated cache warm-up step after deploy, using `navi-hey` (the same CI-capable cache warmer already used in the `majora` project). This covers: a navi-hey config targeting the production proxy, a CircleCI job that runs it after release, a `docker-compose.yml` service to exercise the config locally, and a copy of navi's how-to-use doc under `docs/agents`.

## Context

After deployment, the Tent proxy's file-based cache is currently warmed up by manually navigating the application. `majora` solves this with `navi-hey`: a `warm-up-cache` CircleCI job (requiring the release job) that runs `navi-hey --config .circleci/navi_config.yaml` against `$MAJORA_PRODUCTION_URL`, plus a `majora_navi` docker-compose service for local testing. Weave should follow the same pattern.

## Implementation Steps

### Step 1 — Copy the navi how-to-use doc

Copy `/Users/darthjee/projetos/mine/navi/docs/HOW_TO_USE_NAVI.md` to `docs/agents/HOW_TO_USE_NAVI.md` verbatim (no Weave-specific edits needed — it documents the tool itself, not this project).

### Step 2 — Add the navi config

Create `.circleci/navi_config.yaml`, modeled on majora's `.circleci/navi_config.yaml`:

```yaml
web:
  port: $NAVI_PORT
workers:
  quantity: 5
  retry_cooldown: 10000
  sleep: 500
  max-retries: 50

failure:
  threshold: 0.0

clients:
  default:
    base_url: $WEAVE_PRODUCTION_URL
    timeout: 20000

resources:
  curriculum_person:
    - url: /api/curriculum/person/
      status: 200
```

`backend/curriculum/urls.py` confirms `/api/curriculum/person/` maps to `DefaultPersonView` and is the right path to warm.

### Step 3 — Add the `warm-up-cache` CircleCI job

In `.circleci/config.yml`:

- Add a `warm-up-cache` job using the `darthjee/navi-hey:latest` image, checking out the repo and running `navi-hey --config .circleci/navi_config.yaml` (same shape as majora's job).
- Add it to the `test` workflow, requiring `[release_static_files]` (weave's equivalent of majora's `release` job — the last step before the proxy/static assets are live), with the same tag/branch filters as `release_static_files`.
- The job needs `WEAVE_PRODUCTION_URL` available as a CircleCI environment variable (project-level, like the other deploy secrets such as `SSH_REMOTE_TEMP_DIR`) — set it in the CircleCI project settings, not committed to the repo.

### Step 4 — Add a `navi` service to `docker-compose.yml`

Add a service mirroring majora's `majora_navi`, for testing the config against the local `weave_proxy`:

```yaml
weave_navi:
  image: darthjee/navi-hey:latest
  volumes:
    - .circleci/:/home/node/app
  command: navi-hey --config navi_config.yaml
  environment:
    - WEAVE_PRODUCTION_URL=http://proxy
    - NAVI_PORT=3000
  links:
    - weave_proxy:proxy
  depends_on:
    - weave_proxy
  ports:
    - 0.0.0.0:3100:3000
```

This lets `WEAVE_PRODUCTION_URL` point at the local `weave_proxy` container (`http://proxy`, port 80) for local verification, while CI overrides it to the real production URL.

### Step 5 — Update docker-compose docs

Add `weave_navi` to the "Development Containers" table and the "Port Map" table in `docs/agents/docker-compose.md`, following the existing format.

## Files to Change

- `docs/agents/HOW_TO_USE_NAVI.md` — new file, copied from navi's docs.
- `.circleci/navi_config.yaml` — new file, navi-hey config for `/api/curriculum/person/`.
- `.circleci/config.yml` — add `warm-up-cache` job and wire it into the `test` workflow.
- `docker-compose.yml` — add `weave_navi` service.
- `docs/agents/docker-compose.md` — document the new service and port.

## CI Checks

- `.circleci/`: no local command to fully run a CircleCI job, but `docker compose run --rm weave_navi` exercises the same `navi-hey` config locally (CI job: `warm-up-cache`).

## Notes

- `WEAVE_PRODUCTION_URL` is a new CircleCI project environment variable, analogous to majora's `MAJORA_PRODUCTION_URL` — it must be set manually in CircleCI project settings since it's a secret/environment-specific value, not something this PR can configure end-to-end.
- This is infra/docs work with no Django or React code changes, so no specialist agent split is needed.

# Plan: Generate Admin Assets on Deployment

## Overview

Remove committed Django admin static assets from the repository and generate them at deployment time via `python manage.py collectstatic`. A new CI job (`upload_admin_assets`) will run in parallel with `upload_proxy_files`, and `release_static_files` will depend on both.

## Context

**Current pipeline (relevant jobs):**

```
pytest ─┐
jasmine ─┤─▶ build-and-release ──────────────┐
frontend-checks ─┤─▶ upload_fe_files ──────────┤─▶ release_static_files
checks ─┘─▶ upload_proxy_files ─────────────┘
```

**What changes:**

```
pytest ─┐
jasmine ─┤─▶ build-and-release ──────────────────┐
frontend-checks ─┤─▶ upload_fe_files ────────────┤─▶ release_static_files
checks ─┘─▶ upload_proxy_files ──────────────┤
            └─▶ upload_admin_assets (NEW) ───┘
```

**Django static settings** (`backend/weave/settings.py`):
- `STATIC_ROOT = BASE_DIR / 'assets'` — `collectstatic` writes to `backend/assets/`
- Admin assets land at `backend/assets/admin/`

**Currently committed:** `proxy/static/assets/admin/` — served by Tent at `/static/assets/admin/`.

## Implementation Steps

### Step 1 — Remove committed admin assets and update `.gitignore`

- Delete `proxy/static/assets/admin/` from the repository (`git rm -r proxy/static/assets/admin/`).
- Add `proxy/static/assets/admin/` to `.gitignore`.

### Step 2 — Add `deploy_frontend.sh` to `backend/bin/`

The `darthjee/circleci_weave-base` image does **not** include `deploy_frontend.sh`. Its Dockerfile (`dockerfiles/circleci_weave-base/Dockerfile`) only copies `poetry_builder.sh` from the scripts image — no SSH/rsync upload tooling is included.

The script is available at `/Users/darthjee/projetos/mine/docker/scripts/0.7.0/home/sbin/deploy_frontend.sh`. Copy it to `backend/bin/deploy_frontend.sh` so it is available inside the CI container after the `Set folder` step (which copies all `backend/` contents into the working directory).

> **Note:** This is a temporary solution. In the future, `deploy_frontend.sh` should be extracted into the `circleci_weave-base` Docker image directly.

### Step 3 — Add the `upload_admin_assets` CI job

Add a new job to `.circleci/config.yml` using `darthjee/circleci_weave-base:0.0.4`:

1. Checks out the code.
2. Copies backend files to the working directory (same pattern as `pytest` / `checks` jobs: `cp backend/* ./ -r; rm backend -rf`).
3. Installs Python dependencies via `poetry install --no-root --no-interaction --no-ansi`.
4. Runs `python manage.py collectstatic --noinput` — outputs admin assets to `assets/admin/`.
5. Generates the SSH key file: `bin/deploy_frontend.sh generate_key_file`.
6. Uploads the generated assets to the server: `SOURCE=assets/admin/ deploy_frontend.sh upload` (targeting the path Tent serves `static/assets/admin/` from).

### Step 4 — Update `release_static_files` dependencies

In the workflow definition, add `upload_admin_assets` to the `requires` list of `release_static_files`:

```yaml
- release_static_files:
    requires: [build-and-release, upload_fe_files, upload_proxy_files, upload_admin_assets]
```

### Step 5 — Add `upload_admin_assets` to the workflow

Add the new job to the workflow with the same `requires` and `filters` as `upload_proxy_files`:

```yaml
- upload_admin_assets:
    requires: [pytest, jasmine, frontend-checks, checks]
    filters:
      tags:
        only: /\d+\.\d+\.\d+/
      branches:
        ignore: /.*/
```

## Files to Change

- `.gitignore` — add `proxy/static/assets/admin/`
- `proxy/static/assets/admin/` — remove from repository (`git rm -r`)
- `backend/bin/deploy_frontend.sh` — add script; source file saved at [`docs/agents/plans/X01_generate_admin_assets_on_deployment/deploy_frontend.sh`](deploy_frontend.sh)
- `.circleci/config.yml` — add `upload_admin_assets` job and update `release_static_files` dependencies

## Notes

- `collectstatic` does not require a database connection, so no MySQL sidecar is needed in the new job.
- The upload destination must match the path where `proxy/static/assets/admin/` currently lands on the server, so Tent continues to serve admin assets without configuration changes.
- `backend/bin/deploy_frontend.sh` is a temporary measure. Once the script is baked into the `circleci_weave-base` image, it can be removed from the repo and the CI step updated to call `deploy_frontend.sh` directly.

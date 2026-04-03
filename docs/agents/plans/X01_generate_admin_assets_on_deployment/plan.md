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

### Step 2 — Add the `upload_admin_assets` CI job

Add a new job to `.circleci/config.yml` that:

1. Checks out the code.
2. Copies backend files to the working directory (same pattern as `pytest` / `checks` jobs).
3. Installs Python dependencies via `poetry install`.
4. Runs `python manage.py collectstatic --noinput` to generate admin assets into `assets/admin/`.
5. Generates the SSH key file (`deploy_frontend.sh generate_key_file`).
6. Uploads the generated admin assets to the server at the path Tent serves `static/assets/admin/` from.

> **Open question:** The `deploy_frontend.sh` upload tool is available in `darthjee/vite_weave-base` and `darthjee/tent` images, but not confirmed in `darthjee/circleci_weave-base`. Should a new combined image be used, or should the job be split into two steps (generate in Django image, upload in vite image using `machine: true`)?

### Step 3 — Update `release_static_files` dependencies

In the workflow definition, add `upload_admin_assets` to the `requires` list of `release_static_files`:

```yaml
- release_static_files:
    requires: [build-and-release, upload_fe_files, upload_proxy_files, upload_admin_assets]
```

### Step 4 — Add `upload_admin_assets` to the workflow

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
- `.circleci/config.yml` — add `upload_admin_assets` job and update `release_static_files` dependencies

## Notes

- `collectstatic` does not require a database connection, so no MySQL sidecar is needed in the new job.
- The upload destination must match the path where `proxy/static/assets/admin/` currently lands on the server, so Tent continues to serve admin assets without configuration changes.
- The Docker image for the new job needs both Django/Poetry (for `collectstatic`) and SSH/rsync (for `deploy_frontend.sh`). This is the main open question to resolve before implementation.

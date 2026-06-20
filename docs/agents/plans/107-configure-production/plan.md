# Plan: Configure production

Issue: [107-configure-production.md](../../issues/107-configure-production.md)

## Overview

Production proxy configuration (`prod_proxy_config/`) already exists on this branch from prior work. What remains is updating the CI deployment job (`upload_proxy_files` in `.circleci/config.yml`) so it uploads the new `prod_proxy_config/` into the remote configuration folder and then preserves only the previous `hosts.php`, instead of blanket-copying the whole old configuration directory.

## Context

`prod_proxy_config/` mirrors `docker_volumes/proxy_configuration/` but always serves the frontend statically (no dev mode), and keeps `hosts.php` out of version control (`configure.php` requires it, `hosts.php.sample` is the committed template pointing to `localhost:3030`). The current `upload_proxy_files` CI job's `Copy configuration files` step (`TARGET=configuration/ deploy_frontend.sh copy_files`) copies the entire previous remote `configuration/` directory into the new release, which is the behavior the issue asks to change. The `majora` project already solved the same problem: it uploads its local prod config into the remote temp dir, then copies over only the previous `hosts.php`.

## Implementation Steps

### Step 1 — Verify `prod_proxy_config/` matches majora's pattern

Confirm `prod_proxy_config/configure.php`, `rules/backend.php`, `rules/frontend.php`, and `hosts.php.sample` (already added in commit "Add prod configuration") are consistent with `docker_volumes/proxy_configuration/` minus the dev-mode branching, and that `.gitignore` excludes `prod_proxy_config/hosts.php`. No code changes expected here unless a gap is found.

### Step 2 — Replace the configuration-copy step in `upload_proxy_files`

In `.circleci/config.yml`, under the `upload_proxy_files` job, replace the existing `Copy configuration files` step:

```yaml
- run:
    name: Copy configuration files
    command: TARGET=configuration/ deploy_frontend.sh copy_files
```

with two steps, following majora's `upload_proxy_files` job pattern:

```yaml
- run:
    name: Upload proxy configuration
    command: SOURCE=prod_proxy_config/ SSH_REMOTE_TEMP_DIR=$SSH_REMOTE_TEMP_DIR/configuration/ deploy_frontend.sh upload
- run:
    name: Setup hosts
    command: TARGET=configuration/hosts.php SSH_REMOTE_TEMP_DIR=$SSH_REMOTE_TEMP_DIR/configuration/ deploy_frontend.sh copy_files
```

This uploads the local `prod_proxy_config/` into the remote temp release's `configuration/` subfolder first, then copies only `hosts.php` from the previous live `configuration/` directory into that same subfolder — preserving the real host value without dragging along stale config files.

### Step 3 — Sanity-check `deploy_frontend.sh` semantics

`backend/bin/deploy_frontend.sh`'s `run_upload` rsyncs `$SOURCE` to `$SSH_REMOTE_TEMP_DIR`, and `run_copy_files` does `cp -R $SSH_REMOTE_DIR/$TARGET $SSH_REMOTE_TEMP_DIR/`. No script changes are needed — only the CI step arguments change (`SOURCE`, `SSH_REMOTE_TEMP_DIR`, `TARGET` overrides), matching how majora invokes the same script.

## Files to Change

- `.circleci/config.yml` — replace the `Copy configuration files` step in `upload_proxy_files` with the `Upload proxy configuration` + `Setup hosts` steps described above.

## CI Checks

- No dedicated local command runs `.circleci/config.yml` itself; correctness is validated by CircleCI on push/tag. No `backend`/`frontend` test suite covers this change.

## Notes

- `prod_proxy_config/` already exists on this branch (commit "Add prod configuration"); this plan focuses on the CI deployment change only.
- This is infra/CI configuration, owned by `architect` — it does not fall under either the `backend` or `frontend` specialist scopes.

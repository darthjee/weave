# Configure production

## Context

Weave needs a dedicated production proxy configuration, separate from the development one used in `docker_volumes/proxy_configuration/`. Production has no dev mode and must always serve the frontend from static files. Additionally, the current deployment process for `proxy_files` overwrites the entire remote configuration on every release, which destroys the `hosts.php` file that should persist across deployments.

## What needs to be done

- **Proxy config**: Create `prod_proxy_config/`, mirroring the structure of `docker_volumes/proxy_configuration/`, but:
  - No dev-mode branch — always serve the frontend from static files.
  - Extract the host variable into `prod_proxy_config/hosts.php`, included by `prod_proxy_config/configure.php`.
  - `prod_proxy_config/configure.php` is gitignored (not committed).
  - `prod_proxy_config/hosts.php.sample` is committed and points to `localhost:3030`.
- **Deployment**: Change the `upload_proxy_files` CI step so that, instead of remotely overwriting the old configuration in place, it:
  1. Uploads the local `prod_proxy_config/` into the remote configuration folder.
  2. Right after, copies only the old `hosts.php` file from the previous remote configuration into the new one.
  - Reference implementation: the `majora` project already solved this in `.circleci/config.yml`'s `upload_proxy_files` job — an `Upload proxy configuration` step uploads `prod_proxy_config/` to a remote temp dir, followed by a `Setup hosts` step that copies only `configuration/hosts.php` from the previous config into the new one via `deploy_frontend.sh copy_files`. Apply the same two-step pattern to Weave's CI pipeline.

## Acceptance criteria

- [ ] `prod_proxy_config/` exists with `configure.php` (gitignored), `hosts.php.sample` (committed, pointing to `localhost:3030`), and no dev-mode branch.
- [ ] CI deployment uploads `prod_proxy_config/` to the remote configuration folder, then copies only the previous `hosts.php` into the new folder, instead of overwriting the whole configuration in place.

---
See issue for details: https://github.com/darthjee/weave/issues/107

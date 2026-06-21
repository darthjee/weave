# Plan: Release base images

Issue: [115-release-base-images.md](../../issues/115-release-base-images.md)

## Overview

Add a `scripts/image.sh` script that knows how to check whether a base image needs releasing, build it for a given architecture, and push it to Docker Hub — mirroring the pattern already proven in `darthjee/docker`'s `bin/image.sh`. Wire the root `Makefile`'s `build-base`/`push-base` targets to call it, and add CircleCI `release` jobs (one per base image per arch, amd64 + arm64) to `.circleci/config.yml`, gated to tag-triggered runs and required by the jobs that consume those base images.

## Context

The four base images — `weave-base`, `circleci_weave-base`, `production_weave-base`, `vite_weave-base` — are currently built and pushed only by hand via `make build-base PROJECT=<name>` / `make push-base PROJECT=<name>` (see `Makefile:17-28`). `.circleci/config.yml` hardcodes their tags (e.g. `darthjee/circleci_weave-base:0.0.4` in the `pytest`, `checks`, `upload_admin_assets` jobs; `darthjee/vite_weave-base:0.0.4` in `upload_fe_files`, `release_static_files`) but has no job that builds/releases them, and no `requires:` link tying consumers to a release step. There's also no script-level check for "did this base image actually change" — every push rebuilds from scratch.

`darthjee/docker` solves the same problem: `bin/image.sh` exposes `build|tag|push|test <image> [arch]`, with a `skip_if_unchanged` check that diffs the image's source folder against the previous git tag before pushing, and `.circleci/config.yml` there defines one parameterized `release` job (image + arch params) instantiated once per image/arch with `requires:` chains and `filters: tags: only: /.*/` / `branches: ignore: /.*/`.

## Implementation Steps

### Step 1 — Add `scripts/image.sh`

Create `scripts/image.sh`, adapted from `../docker/bin/image.sh` but matching weave's layout (single `dockerfiles/<image>-base/Dockerfile`, version from the Makefile's `BASE_VERSION`, no per-version folders):

- `image_version <image>` — read the version. Since weave has no `version` file (unlike `docker`), read it from the existing `BASE_VERSION` default in `Makefile` (export it as an env var the script can read, e.g. `BASE_VERSION=${BASE_VERSION:-0.0.4}` with the Makefile passing its own value through).
- `skip_if_unchanged <image>` — same approach as `docker`'s version: find the previous tag with `git tag --sort=-creatordate`, then `git diff <prev_tag>..HEAD --quiet -- dockerfiles/<image>-base/` to decide whether to skip. Exit 0 (skip, no failure) when unchanged.
- `build <image> <arch>` — `docker build --platform linux/<arch> -f dockerfiles/<image>-base/Dockerfile . -t <tag>`, tagging `latest`/`cached`/`<version>` per arch, mirroring `docker/bin/image.sh`'s `build()` (lines 29-57 there).
- `push <image> <arch>` — call `skip_if_unchanged`, `docker login` with `DOCKER_HUB_USERNAME`/`DOCKER_HUB_PASSWORD`, `build`, then `docker push` both the arch-tagged `latest` and `<version>` tags. Use `DOCKER_ID_USER` for the image namespace, consistent with the existing `Makefile`'s `DOCKER_ID_USER` usage.
- CLI dispatch: `scripts/image.sh <build|push> <image> [arch]`, same call shape as `docker/bin/image.sh`.

### Step 2 — Refactor the Makefile

Update `Makefile`'s `build-base` and `push-base` targets (currently inlining `docker build`/`docker tag`/`docker rmi`/`docker push`) to delegate to the new script:

```makefile
build-base:
	scripts/image.sh build $(PROJECT)

push-base:
	scripts/image.sh push $(PROJECT)
```

Keep `PROJECT`, `BASE_VERSION`, `DOCKER_ID_USER` as the existing override points; the script reads them from the environment the Makefile already exports them into.

### Step 3 — Add CircleCI release jobs

In `.circleci/config.yml`:

- Add a parameterized `release` job (`image` and `arch` string parameters, `arch` default `""`), `machine: true`, with a QEMU setup step (`docker run --privileged --rm tonistiigi/binfmt --install all`) before running `scripts/image.sh push << parameters.image >> << parameters.arch >>` — same shape as `docker/.circleci/config.yml`'s `release` job.
- Add one `release` job instance per base image per arch under `workflows.test.jobs`: `release-weave-base`, `release-weave-base-arm64`, `release-circleci_weave-base`, `release-circleci_weave-base-arm64`, `release-production_weave-base`, `release-production_weave-base-arm64`, `release-vite_weave-base`, `release-vite_weave-base-arm64`. Filter all of them to `filters: tags: only: /\d+\.\d+\.\d+/, branches: ignore: /.*/` (same tag pattern already used by `build-and-release` and friends).
- Add `requires:` so that:
  - `pytest`, `checks`, `upload_admin_assets` require `release-circleci_weave-base` (they run `darthjee/circleci_weave-base:0.0.4`)
  - `upload_fe_files`, `release_static_files` require `release-vite_weave-base` (they run `darthjee/vite_weave-base:0.0.4`)
  - Note: `pytest`/`checks`/`frontend-checks`/`jasmine` currently have no `tags:` filter restricting them to releases only — they also run on every branch/PR. Since the new release jobs are tag-only, only add the `requires:` for these consumer jobs in the context where they already run under the tag-only release filters (i.e. leave their branch-build behavior untouched and only gate the additional dependency under the tag workflow). If CircleCI's `requires:` cannot conditionally apply only to tag-triggered runs of an otherwise branch-triggered job, instead leave `pytest`/`checks`/`frontend-checks` as-is (they pull tags by digest already, the base image is expected to exist) and only enforce the `requires:` ordering for `build-and-release`-adjacent jobs (`upload_fe_files`, `upload_admin_assets`, `release_static_files`) which already are tag-only.

### Step 4 — Update hardcoded image tags

No version bump is required by this issue itself, but document that whenever `BASE_VERSION` changes, the corresponding `image:` tag references in `.circleci/config.yml` jobs (`pytest`, `checks`, `upload_admin_assets`, `upload_fe_files`, `release_static_files`) must be bumped to match — this was previously a manual, easy-to-forget step; call this out in a comment near the top of `.circleci/config.yml` or in `docs/agents/docker-compose.md`.

## Files to Change

- `scripts/image.sh` — new script: check/build/push for base images, mirroring `docker/bin/image.sh`.
- `Makefile` — `build-base`/`push-base` targets delegate to `scripts/image.sh`.
- `.circleci/config.yml` — add parameterized `release` job + one instance per base image per arch (amd64/arm64), wire `requires:` from consuming jobs.
- `docs/agents/docker-compose.md` — note the base image release flow and the BASE_VERSION/tag-bump coupling, if that doc covers base image definitions (per `AGENTS.md`'s description of this file).

## CI Checks
- root: `make build-base PROJECT=<image>` / `make push-base PROJECT=<image>` (manual sanity check; no existing CircleCI job runs this — the new `release` job introduced in Step 3 is the first one to)

## Notes
- weave has no `bin/` directory (unlike `docker`'s `bin/image.sh`); the new script goes in `scripts/`, consistent with weave's existing `scripts/deploy.sh`, `scripts/bump_version.sh` convention.
- weave has no per-version `version` file like `docker`; version comes from the Makefile's `BASE_VERSION` var, which the script must receive via environment rather than reading a file.
- Cross-arch (arm64) builds need QEMU emulation (`tonistiigi/binfmt`) added to the new `release` job, since CircleCI's `machine: true` executor here is amd64-only — same requirement `docker`'s pipeline has.
- Open risk: CircleCI's `requires:` only orders jobs within the same workflow run; it cannot "require" a job that didn't run in tag-only mode for a branch-triggered run of the same job name. Step 3 calls this out — verify against CircleCI docs/behavior during implementation and adjust the `requires:` wiring if it doesn't validate as expected (e.g. consider running `pytest`/`checks`/`frontend-checks` only under `release-weave-base`/`release-vite_weave-base`'s digest, rather than gating them at all).

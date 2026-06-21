# Release base images

## Context

On the release flow, base images sometimes get bumped (e.g. `weave-base`, `circleci_weave-base`, `production_weave-base`, `vite_weave-base`), but the CircleCI release flow never builds or pushes them — they currently have to be built and pushed manually. `.circleci/config.yml` references these images by a fixed tag (e.g. `darthjee/circleci_weave-base:0.0.4`) but has no job that releases them, there is no script that detects whether a base image actually changed before rebuilding it, and jobs depending on a base image (e.g. `production_weave` depending on `production_weave-base:<version>`) are not blocked on its release.

A very similar problem was already solved in `darthjee/docker` (`../docker` locally), which has its own `bin/image.sh` script plus per-image, per-arch CircleCI `release` jobs wired with `requires:` chains. This issue follows the same pattern, adapted to weave's structure: a single `Dockerfile` per image directory under `dockerfiles/<image_name>/`, with the version tracked via the `BASE_VERSION` Makefile var (no per-version folder layout like `docker`'s `<image>/<version>/Dockerfile`).

## What needs to be done

- **Script** (mirroring `docker/bin/image.sh`), receiving the operation (`build` / `release` / `check`), the image, and the arch as arguments:
  - Check whether an image needs to be released: an image needs releasing if any file under `dockerfiles/<image_name>/` changed since the previous git tag, or if a dependent Dockerfile bumped the version it references for this base image (e.g. `production_weave`'s `FROM darthjee/production_weave-base:<version>` line). Detection diffs against the previous tag, same as `docker`'s `skip_if_unchanged` (`git tag --sort=-creatordate` + `git diff <prev_tag>..HEAD -- dockerfiles/<image_name>/`). If unchanged, exit 0 without rebuilding.
  - Build the image for a given arch (`docker build --platform linux/<arch> ...`), tagging `latest`/`cached`/`<version>` per arch — same shape as `docker/bin/image.sh`'s `build()`.
  - Release (push) the image: build, then `docker push` both the arch-tagged `latest` and `<version>` tags, after `docker login` using `DOCKER_HUB_USERNAME`/`DOCKER_HUB_PASSWORD` (and `DOCKER_ID_USER` for the image namespace).
- **Makefile**: refactor the root `Makefile`'s `build-base` / `push-base` targets to call the new script instead of inlining the docker commands.
- **CircleCI** (`.circleci/config.yml`):
  - Add a parameterized `release` job (image + arch params), using `machine: true` plus a QEMU setup step (`docker run --privileged --rm tonistiigi/binfmt --install all`) for arm64 cross-builds — same as `docker/.circleci/config.yml`'s `release` job.
  - Add release jobs for each base image — `weave-base`, `circleci_weave-base`, `production_weave-base`, `vite_weave-base` — in both amd64 and arm64, gated with `filters: tags: only: /.*/` / `branches: ignore: /.*/` (tag-triggered releases only).
  - Make existing jobs that consume these images `require` the matching release job: any job using `darthjee/circleci_weave-base:*` or `darthjee/vite_weave-base:*` as its CI image, and any job that builds an image depending on one of these bases (e.g. `production_weave` depending on `production_weave-base`).

## Acceptance criteria

- [ ] A script exists that checks whether a base image needs rebuilding (diff against the previous git tag), builds it for a given arch, and releases (pushes) it to Docker Hub.
- [ ] The root `Makefile`'s `build-base`/`push-base` targets delegate to this script instead of inlining `docker build`/`docker push` commands.
- [ ] `.circleci/config.yml` has a parameterized `release` job (image + arch) with QEMU setup for arm64, and release jobs for `weave-base`, `circleci_weave-base`, `production_weave-base`, `vite_weave-base` in both amd64 and arm64, filtered to tag-triggered runs only.
- [ ] Existing CircleCI jobs that use one of these base images (as CI image or as a build dependency) `require` the corresponding release job(s).

---
See issue for details: https://github.com/darthjee/weave/issues/115

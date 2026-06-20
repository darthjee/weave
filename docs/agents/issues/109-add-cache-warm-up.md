# Add cache warm up

## Context

After deployment, the proxy cache currently has to be warmed up by manually navigating the application. `navi-hey` is a CI-capable cache warmer that already solves this same problem in the `majora` project, via a dedicated CI job and a docker-compose service used to test the configuration locally.

## What needs to be done

- **Docs**: Add a "how to use navi" doc to `docs/agents`, copied from the local navi project source at `/Users/darthjee/projetos/mine/navi/docs/HOW_TO_USE_NAVI.md` (also mirrored at https://github.com/darthjee/navi/blob/main/docs/HOW_TO_USE_NAVI.md).
- **CI**: Add a navi config file (e.g. `.circleci/navi_config.yaml`) configuring the path `/api/curriculum/person/` to expect status `200`.
- **CI**: Add a `warm-up-cache` job to the CI pipeline using the `darthjee/navi-hey:latest` image, running `navi-hey --config .circleci/navi_config.yaml`, mirroring the `warm-up-cache` job in majora's `.circleci/config.yml`.
- **Local testing**: Add a `navi` service to `docker-compose.yml` to test the configuration locally, using image `darthjee/navi-hey:latest`, mounting the `.circleci/` config directory, and running `navi-hey --config navi_config.yaml`, mirroring majora's `majora_navi` service in `docker-compose.yml`.

Reference implementation: `/Users/darthjee/projetos/mine/majora/.circleci/config.yml` (job `warm-up-cache`), `/Users/darthjee/projetos/mine/majora/.circleci/navi_config.yaml`, and `/Users/darthjee/projetos/mine/majora/docker-compose.yml` (service `majora_navi`).

## Acceptance criteria

- [ ] `docs/agents/HOW_TO_USE_NAVI.md` exists, copied from navi's docs.
- [ ] `.circleci/navi_config.yaml` exists and configures `/api/curriculum/person/` to expect status `200`.
- [ ] CI pipeline has a `warm-up-cache` job running `navi-hey` against the configured paths after deploy.
- [ ] `docker-compose.yml` has a `navi` service that can be used to test the warm-up configuration locally.

---
See issue for details: https://github.com/darthjee/weave/issues/109

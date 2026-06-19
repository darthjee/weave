#!/usr/bin/env bash
set -euo pipefail
set -x

docker-compose run weave_fe npm test
docker-compose run weave_fe npm run lint

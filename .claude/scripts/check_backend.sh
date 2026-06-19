#!/usr/bin/env bash
set -euo pipefail
set -x

docker-compose run weave_tests poetry run pytest
docker-compose run weave_tests poetry run ruff check .

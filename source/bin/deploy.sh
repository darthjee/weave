#!/bin/bash

function migrate() {
  ./bin/configure_database.sh migrate
}

function build() {
  poetry run python manage.py collectstatic --noinput
}

function all() {
  migrate
  build
}

case "$1" in
  migrate)
    migrate
    ;;
  build)
    build
    ;;
  all)
    all
    ;;
  *)
    echo "Usage: $0 {migrate|build|all}"
    ;;
esac

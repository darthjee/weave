#!/bin/bash

function migrate() {
  ./bin/configure_database.sh migrate
}

function build() {
  poetry run python manage.py collectstatic --noinput
}

function upload() {
  # TODO: Implement upload logic
  echo "Upload function not implemented yet."
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
  upload)
    upload
    ;;
  all)
    all
    ;;
  *)
    echo "Usage: $0 {migrate|build|upload|all}"
    ;;
esac

#!/bin/bash

function migrate() {
  ./bin/configure_database.sh migrate
}

function build() {
  poetry run python manage.py collectstatic --noinput
}

upload() {
  LOCAL_DIR="static"

  rsync -avz -e "ssh -p $SSH_PORT" static/ $SSH_USER@$SSH_HOST:$SSH_REMOTE_DIR
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

#!/bin/bash

function migrate() {
  ./bin/configure_database.sh migrate
}

function build() {
  poetry run python manage.py collectstatic --noinput
}

upload() {
  LOCAL_DIR="static"

  SSH_KEY_FILE=$(createSshKeyFile)

  SSH_REMOTE_TEMP_DIR="${SSH_REMOTE_DIR}_$RANDOM"
  SSH_REMOTE_OLD_DIR="${SSH_REMOTE_DIR}_old"

  ssh -i "$SSH_KEY_FILE" -p "$SSH_PORT" -o StrictHostKeyChecking=no $SSH_USER@$SSH_HOST "mkdir -p $SSH_REMOTE_DIR"
  ssh -i "$SSH_KEY_FILE" -p "$SSH_PORT" -o StrictHostKeyChecking=no $SSH_USER@$SSH_HOST "mkdir -p $SSH_REMOTE_TEMP_DIR"
  scp -i "$SSH_KEY_FILE" -P "$SSH_PORT" -o StrictHostKeyChecking=no -r static/* $SSH_USER@$SSH_HOST:$SSH_REMOTE_TEMP_DIR
  ssh -i "$SSH_KEY_FILE" -p "$SSH_PORT" -o StrictHostKeyChecking=no $SSH_USER@$SSH_HOST "mv $SSH_REMOTE_DIR $SSH_REMOTE_OLD_DIR; mv $SSH_REMOTE_TEMP_DIR $SSH_REMOTE_DIR; rm -rf $SSH_REMOTE_OLD_DIR"

  rm -f "$SSH_KEY_FILE"
}

function createSshKeyFile() {
  SSH_KEY_FILE=$(mktemp)
  echo "$SSH_PRIVATE_KEY" > "$SSH_KEY_FILE"
  chmod 600 "$SSH_KEY_FILE"
  echo $SSH_KEY_FILE
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

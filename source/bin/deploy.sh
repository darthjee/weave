#!/bin/bash

function migrate() {
  ./bin/configure_database.sh migrate
}

function build() {
  poetry run python manage.py collectstatic --noinput
}

upload() {
  LOCAL_DIR="static"

  # Cria arquivo temporário para a chave privada
  SSH_KEY_FILE=$(mktemp)
  echo "$SSH_PRIVATE_KEY" > "$SSH_KEY_FILE"
  chmod 600 "$SSH_KEY_FILE"

  rsync -avz -e "ssh -p $SSH_PORT -i $SSH_KEY_FILE" static/ $SSH_USER@$SSH_HOST:$SSH_REMOTE_DIR

  # Remove arquivo temporário
  rm -f "$SSH_KEY_FILE"
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

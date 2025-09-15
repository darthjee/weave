#!/bin/bash

function migrate() {
  ./bin/configure_database.sh migrate
}

function all() {
  migrate
}

case "$1" in
  migrate)
    migrate
    ;;
  all)
    all
    ;;
  *)
    echo "Usage: $0 {migrate|all}"
    ;;
esac

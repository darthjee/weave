#!/bin/bash

create() {
  echo "Creating database..."
  mysql -h "$WEAVE_MYSQL_HOST" -u "$WEAVE_MYSQL_USER" -p"$WEAVE_MYSQL_PASSWORD" < "mysql/create_dev_database.sql"
}

migrate() {
  echo "Running migrations..."
  python manage.py migrate
}

all() {
  create
  migrate
}

case "$1" in
  create)
    create
    ;;
  migrate)
    migrate
    ;;
  all)
    all
    ;;
  *)
    echo "Usage: $0 {create|migrate|all}"
    ;;
esac

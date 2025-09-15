#!/bin/bash

wait_for_db() {
  MAX_RETRIES=${MAX_RETRIES:-30}
  RETRY_INTERVAL=${RETRY_INTERVAL:-2}

  echo "Waiting for database in $WEAVE_MYSQL_HOST:$WEAVE_MYSQL_PORT ..."

  for ((i=1; i<=MAX_RETRIES; i++)); do
    if echo "" | telnet $WEAVE_MYSQL_HOST $WEAVE_MYSQL_PORT; then
      exit 0
    fi

    sleep "$RETRY_INTERVAL"
  done

  echo "Database inaccessible after $MAX_RETRIES attempts."
  exit 1

}

create() {
  echo "Creating database..."
  mysql -h "$WEAVE_MYSQL_HOST" -u "$WEAVE_MYSQL_USER" -p"$WEAVE_MYSQL_PASSWORD" < "mysql/create_dev_database.sql"
}

migrate() {
  echo "Running migrations..."
  poetry run python manage.py migrate
}

all() {
  wait_for_db
  create
  migrate
}

case "$1" in
  wait)
    wait_for_db
    ;;
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
    echo "Usage: $0 {wait|create|migrate|all}"
    ;;
esac

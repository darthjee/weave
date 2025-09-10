#!/bin/bash

case "$1" in
  create)
    mysql -h "$WEAVE_MYSQL_HOST" -u "$WEAVE_MYSQL_USER" -p"$WEAVE_MYSQL_PASSWORD" < "mysql/create_dev_database.sql"
    ;;
  migrate)
    python manage.py migrate
    ;;
  *)
    echo "Usage: $0 {create|migrate}"
    ;;
esac

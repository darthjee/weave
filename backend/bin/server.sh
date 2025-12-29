#!/bin/bash

if [ "$CREATE_DB" = "true" ]; then
  bin/configure_database.sh all
fi

if [ "$STAGE" = "production" ]; then
  poetry run gunicorn weave.wsgi:application --bind 0.0.0.0:8080 --access-logfile - --error-logfile - --log-level info
else
  poetry run python manage.py runserver 0.0.0.0:8080
fi

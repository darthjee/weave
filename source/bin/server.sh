#!/bin/bash

if [ "$STAGE" = "production" ]; then
  poetry run gunicorn weave.wsgi:application --bind 0.0.0.0:8080
else
  bin/configure_database.sh all
  poetry run python manage.py runserver 0.0.0.0:8080
fi

#!/bin/bash

if [ "$STAGE" = "production" ]; then
  gunicorn weave.wsgi:application --bind 0.0.0.0:8080
else
  python manage.py runserver 0.0.0.0:8080
fi

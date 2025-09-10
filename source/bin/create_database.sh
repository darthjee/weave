#!/bin/bash

mysql -h "$WEAVE_MYSQL_HOST" -u "$WEAVE_MYSQL_USER" -p"$WEAVE_MYSQL_PASSWORD" < "mysql/create_dev_database.sql"
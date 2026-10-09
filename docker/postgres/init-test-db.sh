#!/bin/sh
# Runs once on an empty data volume: creates the database used by the test suite.
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-SQL
    CREATE DATABASE ${POSTGRES_DB}_test OWNER ${POSTGRES_USER};
SQL

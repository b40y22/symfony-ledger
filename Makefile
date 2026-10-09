export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

EXEC := docker compose exec php
CONSOLE := $(EXEC) bin/console

.PHONY: build up down restart ps logs sh install db-test migrate test stan lint fix check

build:
	docker compose build

up:
	docker compose up -d --wait

down:
	docker compose down

restart: down up

ps:
	docker compose ps

logs:
	docker compose logs -f --tail=100

sh:
	$(EXEC) bash

install:
	$(EXEC) composer install

migrate:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration

# The test database itself is created by docker/postgres/init-test-db.sh; this keeps its schema current.
db-test:
	$(CONSOLE) doctrine:database:create --env=test --if-not-exists
	$(CONSOLE) doctrine:migrations:migrate --env=test --no-interaction --allow-no-migration

test:
	$(EXEC) bin/phpunit

stan:
	$(EXEC) vendor/bin/phpstan analyse --memory-limit=512M

lint:
	$(EXEC) vendor/bin/php-cs-fixer fix --dry-run --diff

fix:
	$(EXEC) vendor/bin/php-cs-fixer fix

check: lint stan test

# Symfony Ledger

A double-entry ledger service with wallets: accounts, entries, transfers, idempotent writes and asynchronous settlement.
It's a learning and portfolio project focused on Symfony's strengths: Doctrine, Messenger, Workflow and Security.

> Status: week 1, project skeleton. The domain model lands next.

## Stack

| Layer | Choice |
|---|---|
| Runtime | PHP 8.5, Symfony 8.1 |
| Persistence | PostgreSQL 17, Doctrine ORM + Migrations |
| Messaging | RabbitMQ 4 via Symfony Messenger (AMQP) |
| Quality | PHPUnit, PHPStan level 8, PHP-CS-Fixer (`@Symfony`), GitHub Actions |

## Running locally

The only requirements are Docker (with Compose v2) and `make`. PHP never runs on the host.

```bash
make build    # build the PHP image as your host UID, so files stay yours
make up       # start nginx, php-fpm, postgres, rabbitmq and wait for health
make install  # composer install
make check    # coding standards + static analysis + tests
```

| Service | URL | Override |
|---|---|---|
| API | http://localhost:8090/health | `HTTP_PORT` |
| PostgreSQL | `localhost:5433` (user/pass/db: `ledger`) | `POSTGRES_PORT` |
| RabbitMQ UI | http://localhost:15673 (`ledger` / `ledger`) | `RABBITMQ_UI_PORT` |

The ports are overridden from the shell, e.g. `HTTP_PORT=8091 make up`.

Other targets: `make sh` (shell in the PHP container), `make fix`, `make migrate`, `make db-test`, `make logs`.

## Health check

`GET /health` does a real `SELECT 1` round-trip against PostgreSQL. It returns `200 {"status":"ok"}`, or `503` if the database is unreachable.

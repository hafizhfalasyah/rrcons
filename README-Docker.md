# RRCONS Docker Development Environment

## Current implementation status

Docker migration is being implemented incrementally to preserve the behavior of the production application.

| Phase | Status |
|---|---|
| Phase 3.1 — Docker Scaffolding | Complete |
| Phase 3.2 — PHP and Apache Image | Complete |
| Phase 3.3 — MariaDB Service | Complete |
| Phase 3.4 — Environment Configuration | Complete |
| Phase 3.5 — Node and Vite Service | Pending |
| Phase 3.6 — Mailpit Service | Pending |
| Phase 3.7 — Storage and Permission Handling | Pending |
| Phase 3.8 — Initial Build Validation | Pending |

## Intended local architecture

The completed environment will use PHP 8.5.7 with Apache 2.4, MariaDB 10.6.27, Composer inside the PHP container, Node.js in a separate service, Mailpit for local email capture, UTC timezone, persistent uploaded media, file-based Laravel cache and sessions, and synchronous queues.

Production remains on shared hosting. Docker files are local development infrastructure and are not part of the shared-hosting runtime.

## Environment model

Docker Compose reads local values from `.env.docker`, while Laravel receives the resolved runtime variables directly through the `app` service. This avoids relying on the host PHP installation and prevents the production `.env` file from being copied into the Docker configuration.

Create the active local environment file:

```bash
cp .env.docker.example .env.docker
```

The active `.env.docker` file is ignored by Git and must never contain production credentials that are not required for local parity.

## Required local values

Before the first startup, replace these placeholders:

```dotenv
RRCONS_DB_PASSWORD=change-me
RRCONS_DB_ROOT_PASSWORD=change-root-me
```

Generate a Laravel application key after the PHP image can run:

```bash
docker compose --env-file .env.docker run --rm app php artisan key:generate --show
```

Copy the returned `base64:...` value into:

```dotenv
RRCONS_APP_KEY=base64:generated-value
```

The command only displays a key and does not edit project files.

If the imported production database contains values encrypted with Laravel's `APP_KEY`, use the corresponding key securely in the local `.env.docker`; otherwise those values cannot be decrypted. Do not commit that key.

## Laravel runtime values

The `app` service receives the following production-aligned defaults:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8082
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
BROADCAST_DRIVER=log
```

`APP_URL` must match `RRCONS_APP_PORT`. If the port is changed, update both values, for example:

```dotenv
RRCONS_APP_PORT=8090
RRCONS_APP_URL=http://localhost:8090
```

The application timezone remains UTC through both the PHP configuration and container `TZ` value.

## Database environment synchronization

The same Compose values initialize MariaDB and configure Laravel:

```dotenv
RRCONS_DB_DATABASE=rrcons
RRCONS_DB_USERNAME=rrcons
RRCONS_DB_PASSWORD=local-secret
```

MariaDB initialization variables are only applied when `rrcons-db-data` is empty. Changing these values later does not automatically update users or passwords in an existing volume.

Laravel connects only through the internal Compose hostname:

```dotenv
DB_HOST=db
DB_PORT=3306
```

The database is not exposed to Windows by default.

## Filesystem, cache, session, and queue

The audited application behavior is represented by:

```dotenv
RRCONS_FILESYSTEM_DISK=public
RRCONS_CACHE_DRIVER=file
RRCONS_SESSION_DRIVER=file
RRCONS_QUEUE_CONNECTION=sync
```

`FILESYSTEM_DISK=public` aligns local URL generation and uploaded-media behavior with `storage/app/public`. The required symlink and writable-directory handling are intentionally deferred to Phase 3.7.

Redis, queue workers, Supervisor, and cron are not part of this architecture.

## Mail behavior before Phase 3.6

Until Mailpit is added, local mail uses the Laravel `log` mailer:

```dotenv
RRCONS_MAIL_MAILER=log
```

This prevents accidental delivery through production SMTP credentials. Phase 3.6 will replace this with the internal Mailpit SMTP service.

## Configuration boundaries

Container startup does not automatically run:

```text
composer install
npm ci
php artisan key:generate
php artisan migrate
php artisan migrate:fresh
php artisan db:seed
SQL dump import
```

Every dependency, schema, and data operation remains explicit.

## Phase 3.4 validation commands

Create and edit the active environment file:

```bash
cp .env.docker.example .env.docker
```

Validate the resolved Compose configuration:

```bash
docker compose --env-file .env.docker config
```

After building the PHP image, inspect the variables visible to the container:

```bash
docker compose --env-file .env.docker run --rm app sh -lc \
  'printf "%s\n" "$APP_ENV" "$APP_URL" "$DB_HOST" "$DB_DATABASE" "$CACHE_DRIVER" "$FILESYSTEM_DISK" "$QUEUE_CONNECTION" "$SESSION_DRIVER"'
```

After dependencies are installed, Laravel configuration can be checked with:

```bash
docker compose --env-file .env.docker run --rm app php artisan about
```

## Dependency preservation

Use only:

```bash
composer install
npm ci
```

Do not use `composer update`, `npm update`, or platform-requirement bypasses.

## Current limitation

Node/Vite, Mailpit, storage permission automation, dependency installation, database import, and full application startup validation remain for later phases.

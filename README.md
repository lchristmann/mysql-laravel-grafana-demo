# MySQL Laravel Grafana Demo <!-- omit in toc -->

![](https://badgen.net/badge/Docker%20Compose/2.36.2/cyan)
![](https://badgen.net/badge/MySQL/8.4/blue)
![](https://badgen.net/badge/Laravel/12/red)
![](https://badgen.net/badge/Grafana/12/orange)

This project shall demonstrate a setup with

- 2 MySQL databases holding business data
- a Laravel API querying that data and serving it via a JSON API
- and a Grafana dashboard consuming the API

Content-wise, the example is geared towards a fictional business, that has users (that can have sub-users),
protocols that are created and partially signed with QES ([Qualified Electronic Signature](https://en.wikipedia.org/wiki/Qualified_electronic_signature)), and valuations (of products) being done.

## Table of Contents <!-- omit in toc -->

- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
  - [Database Schema](#database-schema)
  - [API Schema](#api-schema)
- [Start and Stop](#start-and-stop)
- [One-time Setup](#one-time-setup)
- [Database Access \& Data (from Seeding) Explained](#database-access--data-from-seeding-explained)
  - [Example Query](#example-query)

## Tech Stack

- 2 [MySQL](https://www.mysql.com/) databases
- 1 [Laravel 12](https://laravel.com/docs/12.x) API
  - exposing a JSON API for querying the two MySQL databases
- 1 [Grafana](https://grafana.com/docs/grafana/latest/setup-grafana/installation/docker/) Dashboard
  - using the [Grafana Infinity data source plugin](https://grafana.com/docs/plugins/yesoreyeram-infinity-datasource/latest/)

With the [Docker Compose](https://docs.docker.com/compose/) setup being based on my [lchristmann/selfhosted-tracker-backend](https://github.com/lchristmann/selfhosted-tracker-backend) project, which already implements a JSON API, and is itself based on the official [Docker Compose Laravel setup](https://docs.docker.com/guides/frameworks/laravel/) example.

## Architecture

### Database Schema

![Database schema](docs/db-schema.drawio.svg)

### API Schema

Please refer to the [API Documentation](docs/API-DOCUMENTATION.md).

### Grafana

Visit the Grafana instance at http://localhost:3000 (the default credentials are user `admin` and password `admin`).

## Start and Stop

If this is a fresh project, do the [One-time Setup](#one-time-setup) first.

```shell
docker compose up -d
```

```shell
docker compose down
```

## One-time Setup

1. Copy the .env.dev.example file to .env and adjust any necessary environment variables:

```bash
cp .env.example .env
```

2. Start the Docker Compose Services:

```bash
docker compose up -d
```

3. Install Laravel Dependencies:

```bash
docker compose exec workspace bash
composer install
```

4. Run Migrations and Seeding:

> You should also do this at least every 3 weeks, so `last_login` is renewed for the users. (Users are only considered 'active' if their `last_login` was within the last 30 days.)

```bash
docker compose exec workspace bash
php artisan migrate:fresh --seed
```

5. Generate Application Key

```shell
docker compose exec workspace php artisan key:generate
```

6. Access the API (e.g. http://localhost/api/metrics/qes/total-unlocked-users)

Visit the [API Documentation](docs/API-DOCUMENTATION.md), which has a link to the public Postman collection.

## Database Access & Data (from Seeding) Explained

You can of course access the database via Laravel, but if you want to view it directly, here you go:

```shell
docker compose exec postgres bash
psql -d app -U laravel
```

```shell
\dt
\d users
SELECT * FROM users LIMIT 10;
```

Notes on what data was created during seeding:

- 60 users + 20 to 40 sub-users (created randomly between 1 and 2 years ago; last_login has happened with 80% chance during the last 9 days)
- 400 protocols (created randomly during the last year; 50% they're signed with QES (by a user that is activated for QES))
- 400 valuations (created randomly during the last year (for a user that is activated for taxierungen))

Definitions:

- active user: `last_login` during the last 30 days 

### Example Query

Show all users who have sub-users, odered by the count of sub-users.

```sql
SELECT
    u.id AS user_id,
    u.name,
    COUNT(c.id) AS child_count
FROM users u
JOIN users c ON c.parent_user_id = u.id
GROUP BY u.id, u.name
HAVING COUNT(c.id) > 0
ORDER BY child_count DESC;
```

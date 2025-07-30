# MySQL Laravel Grafana Demo <!-- omit in toc -->

![](https://badgen.net/badge/Docker%20Compose/2.36.2/cyan)
![](https://badgen.net/badge/MySQL/9.3/blue)
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
  - [Database](#database)
  - [API](#api)
  - [Grafana](#grafana)
- [Start and Stop](#start-and-stop)
  - [Renew the data](#renew-the-data)
- [One-time Setup](#one-time-setup)
- [Testing](#testing)

## Tech Stack

- 2 [MySQL](https://www.mysql.com/) databases
- 1 [Laravel 12](https://laravel.com/docs/12.x) API
  - exposing a JSON API for querying the two MySQL databases
- 1 [Grafana](https://grafana.com/docs/grafana/latest/setup-grafana/installation/docker/) Dashboard
  - using the [Grafana Infinity data source plugin](https://grafana.com/docs/plugins/yesoreyeram-infinity-datasource/latest/)

With the [Docker Compose](https://docs.docker.com/compose/) setup being based on my [lchristmann/selfhosted-tracker-backend](https://github.com/lchristmann/selfhosted-tracker-backend) project, which already implements a JSON API, and is itself based on the official [Docker Compose Laravel setup](https://docs.docker.com/guides/frameworks/laravel/) example.

> Note: for a live system a third MySQL database could expand what's possible to visualize by enabling historic data that goes beyond what the business data already offers.
> One would therefor [schedule a recurring task in Laravel](https://laravel.com/docs/12.x/scheduling), that runs queries on the two MySQL DBs
> and stores the result in the third MySQL DB along with a date or timestamp.

## Architecture

### Database

This is the database schema as entity relationship diagram (ERD).

![Database schema](docs/db-schema.drawio.svg)

Please refer to the [Data(base) Guide](docs/DATA-GUIDE.md) for information about the seeded data, running migrations,  PhpMyAdmin and running SQL queries.

### API

Please refer to the [API Documentation](docs/API-DOCUMENTATION.md).

### Grafana

Please refer to the [Grafana Documentation](docs/GRAFANA-DOCUMENTATION.md).

## Start and Stop

If this is a fresh project, do the [One-time Setup](#one-time-setup) first.

```shell
docker compose up -d
```

```shell
docker compose down
```

### Renew the data

This should be done at least every 3 weeks, so `last_login` is renewed for the users (or else `active` users created during seeding, aren't considered `active` anymore).

```bash
docker compose exec workspace bash
php artisan migrate:refresh --seed
```


## One-time Setup

1. Copy the `.env.dev.example` file to `.env`:

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

```bash
docker compose exec workspace bash
php artisan db:drop-users-table # not relevant on first ever migration
php artisan migrate:fresh --seed
```

5. Generate Application Key

```shell
docker compose exec workspace php artisan key:generate
```

6. Access the API (e.g. [http://localhost/api/metrics/qes/total-unlocked-users](http://localhost/api/metrics/qes/total-unlocked-users))

Just make sure that it works (i.e. it returns some JSON response) - you can use your browser here.

If it doesn't work yet, ensure that

- you have `http` not `https` in the URL scheme
- all 6 Docker Compose services are up and running (`docker compose ps`)
  - in case some are missing, re-run `docker compose up -d`

Also see the [API Documentation](docs/API-DOCUMENTATION.md).

7. Visit the [Grafana Dashboard](http://localhost:3000/d/fb6c8c15-7315-43e0-af17-d5fc0995955d/mysql-laravel-grafana-demo)

Also see the [Grafana Documentation](docs/GRAFANA-DOCUMENTATION.md).

## Testing

I've written the API's tests using the [Pest](https://pestphp.com/) framework, which is built on PHPUnit.

> The tests are based on the seeding, but robust enough to avoid flakiness.<br>
> If some, like the `qes/active-users` fail, make sure you've re-run the seeding sometime in the last 21 days.

Execute the tests:

```shell
docker compose exec workspace bash
php artisan test
```

As shown in the Laravel docs on [Running tests](https://laravel.com/docs/12.x/testing#running-tests), you can run tests
[in parallel](https://laravel.com/docs/12.x/testing#running-tests-in-parallel), [with coverage](https://laravel.com/docs/12.x/testing#reporting-test-coverage)
or [list the ten slowest](https://laravel.com/docs/12.x/testing#profiling-tests):

```shell
docker compose exec workspace bash
php artisan test --parallel
```

```shell
docker compose exec workspace bash
php artisan test --coverage
```

```shell
docker compose exec workspace bash
php artisan test --profile
```

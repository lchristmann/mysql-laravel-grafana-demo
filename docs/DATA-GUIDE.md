# Data(base) Guide <!-- omit in toc -->

## Table of Contents <!-- omit in toc -->

- [PhpMyAdmin](#phpmyadmin)
- [Running Migrations (Special here!)](#running-migrations-special-here)
- [Regarding the Seeded Data](#regarding-the-seeded-data)
- [Example Queries](#example-queries)
- [Adaption of Eloquent Models](#adaption-of-eloquent-models)

## PhpMyAdmin

I added PhpMyAdmin, which you can access on http://localhost:8001 with username `laravel` and password `secret`.

## Running Migrations (Special here!)

When you `migrate:fresh`, it only drops the tables of the default connection `mysql` (with the `app` database).
So you have to drop the `users` table in the `users` database by running an extra Artisan command as shown below.

```shell
docker compose exec workspace bash
php artisan db:drop-users-table
php artisan migrate:fresh --seed
```

After that you can re-create the database and re-seed with `migrate:refresh --seed` - it drops, recreates and seeds the `users` table correctly.

```shell
docker compose exec workspace bash
php artisan migrate:refresh --seed
```

## Regarding the Seeded Data

Notes on what data was created during seeding:

- 60 users + 20 to 40 sub-users (created randomly between 1 and 2 years ago; last_login has happened with 80% chance during the last 9 days)
- 400 protocols (created randomly during the last year; 50% they're signed with QES (by a user that is activated for QES))
- 400 valuations (created randomly during the last year (for a user that is activated for taxierungen))

Definitions:

- active user: `last_login` during the last 30 days

## Example Queries

You can simply run the queries in the "SQL" Tab in PhpMyAdmin.

**Show all users who have sub-users, odered by the count of sub-users.**

> Run this query having selected the `users` database.

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

**Show users along with and sorted by how many protocols they have.**

> This query uses two tables from different databases: `users.users` and `app.protocols`. Run it anywhere.

```sql
SELECT u.id, u.name, u.email, COUNT(*) AS protocols_count
FROM users.users u
LEFT JOIN app.protocols p ON u.id = p.user_id
GROUP BY u.id, u.name, u.email
ORDER BY protocols_count DESC;
```

## Adaption of Eloquent Models

I had to add the fully qualified table name (like `protected $table = 'app.protocols';` to all the models,
or else the relations (like in `User::whereHas(Protocol:class)` wouldn't work - [as can be read here](https://laracasts.com/discuss/channels/eloquent/how-to-properly-use-2-database-relationships)).

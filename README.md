## Development setup

```bash
git pull --all
cp .env.example .env
composer install
php artisan key:generate
```

-   create database name `budgeting`

```bash
php artisan migrate
php artisan config:clear
php artisan config:cache
php artisan serve
```

```bash
yarn install
yarn dev
```

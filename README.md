# Lottery System

This is a lottery system backend which is developed in Laravel 8 and PostgreSQL.

### Prerequisition

1. PHP 7.3
2. Postgres

### Project setup

```
composer install --ignore-platform-reqs
```

### Database configuration

Copy '.env.example' as '.env' and change below lines according to database configuration

Default database should be postgres.
Default username should be postgres.

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=<database_name>
DB_USERNAME=<database_username>
DB_PASSWORD=<database_password>
```

### Create tables

Remarks: It will clear all previous / remaining data.

```
php artisan migrate:refresh
```

### For mockup data generation

Please `composer dump-autoload` to reload those files if there is any change.

Generate Mockup User Data

```
php artisan mockup:createUser
```

### Serve the application for development

```
php -S localhost:8000 -t public
```

## Limitations

1. The draw of tickets periodically and continuously at every x minutes depends on the frontend api call. As this system is developed in laravel, it is hard to handle process that are needed to be called repeatedly and periodically. Scheduler in Laravel is needed to implement these repeated functions, which requires to run as a cron job. Due to cron job requires modifying the cron file in the local drive, it might not be a good solution for this system. Therefore, the ticket drawing event depends on the api request from the frontend. Frontend is required to call that api repeatedly with the given interval to start each draw.
2. After each draw, the backend response with the win ticket details and the draw details. Each contestent frontend need to call apis to get the updated information of their tickets instead of getting whether they win or not directly. Frequently calls of api for getting tickets information may needed. Websocket may be a better solution to this kind of situation.  
3. Under the situation of not knowing what actually needs to be display on the client side, methods defined in controllers are mostly generalized methods, so as to suit different combination of data that the client side needed. If how data needed to be displayed in client side is knowable, more precise methods could be developed to prevent getting extra data from the database thus reduce the complexity of the data response to the frontend.

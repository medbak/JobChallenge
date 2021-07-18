# Job Challenge

## Prepare Environment

1) Create database and configure database connection in .env file
   ```
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=homestead
   DB_USERNAME=homestead
   DB_PASSWORD=secret
   ```
2) Run database migration
   ```
   php artisan migrate
   ```

## Start Application 
1) Launch Application (choose the port example 1234)
   ```
   php -S localhost:1234 -t public
   url : http://localhost:1234
   ```

2) Run Tests
   ```
   vendor/bin/phpunit
   ```


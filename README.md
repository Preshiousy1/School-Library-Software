# School-Library-Software

School Library Software for 23IT

You need to have Docker and Docker Compose installed on your server to proceed using this PHP environment.

This is a PHP development environment used to run Laravel applications. The following five separate service containers will be used:

-   An `app` service running PHP7.4-FPM.
-   A `db` service running MySQL 5.7.
-   A `phpmyadmin` service for viewing `db` tables
-   An `nginx` service that uses the `app` service to parse PHP code before serving the Laravel application to the final user.
-   A `cron` service running PHP7.4-fpm-alpine that handles the backgroud processes.

## Running the application

-   To get started, pull this repo into a root directory

```bash
git clone https://github.com/Preshiousy1/School-Library-Software.git
```

-   cd into the project

```bash
cd School-Library-Software
```

-   Copy the env file

```bash
 cp .env.example .env
```

-   Build and start docker-compose

```bash
docker-compose up -d --build
```

-   install all composer dependencies

```bash
docker-compose exec app composer install
```

-   clear cache

```bash
docker-compose exec app php artisan optimize
```

-   migrate the database

```bash
docker-compose exec app php artisan migrate
```

### set up your own superadmin username and password at .env file

```bash
ADMIN_USERNAME= admin(default)
ADMIN_PASSWORD= password(default)
```

-   run the database seeder to automatically create super admin

```bash
docker-compose exec app php artisan db:seed
```

-   Now go to your browser and access your server’s domain name or IP address on port `8000`: `http://server_domain_or_IP:8000`. In case you are running this demo on your local machine, use `http://localhost:8000` to access the application from your browser.

-   You can watch the database with `phpmyadmin` at `http://localhost:8081` with login details:

```bash
server = db
username = root
password = (empty)
```

## Using the application

-   You can test all routes using the following postman collection: `. Library.postman_collection.json` it in the application's root director. Just import it into your postman desktop app to start running endpoints.

-   The root url is an environment variable that is currently `http://127.0.0.1:8000/api` you can chane it to suite your server ip address
-   the values for the following env variables can be changed as well in the .env file. This variables determine the time used in the background processes in `hours`

```bash
CLOSE_BORROW_REQUESTS=24
SUSPEND_BORROW_USERS=72
```
